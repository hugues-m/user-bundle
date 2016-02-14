<?php

namespace HMLB\UserBundle\User;

use DateTime;
use HMLB\DDD\Entity\AggregateRoot;
use HMLB\DDD\Entity\Identity;
use HMLB\DDD\Validation\Assertion;
use HMLB\UserBundle\Event\EmailChanged;
use HMLB\UserBundle\Event\EmailConfirmed;
use HMLB\UserBundle\Event\EmailValidationRequested;
use HMLB\UserBundle\Event\PasswordChanged;
use HMLB\UserBundle\Event\PasswordReset;
use HMLB\UserBundle\Event\PasswordResetRequested;
use HMLB\UserBundle\Event\UserRegistered;
use HMLB\UserBundle\Exception\EmailAlreadyConfirmedException;
use HMLB\UserBundle\Exception\InvalidEmailConfirmationTokenException;
use HMLB\UserBundle\Exception\InvalidPasswordResettingTokenException;
use HMLB\UserBundle\Exception\PasswordResettingNotRequestedException;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class User implements AdvancedUserInterface, AggregateRoot, ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @var Identity
     */
    protected $id;

    /**
     * @var Role[]
     */
    protected $roles = [];

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * The salt to use for hashing.
     *
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * @var DateTime
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * Random string sent to the user email address in order to reset the password.
     *
     * @var string
     */
    protected $resettingToken;

    /**
     * @var DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * @var DateTime
     */
    protected $updated;

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @var bool
     */
    protected $expired = false;

    /**
     * @var DateTime
     */
    protected $expiresAt;

    /**
     * @var bool
     */
    protected $credentialsExpired = false;

    /**
     * @var DateTime
     */
    protected $credentialsExpireAt;

    /**
     * @param string                       $username
     * @param string                       $email
     * @param string                       $plainPassword
     * @param UserPasswordEncoderInterface $encoder
     * @param array                        $roles
     * @param bool                         $enable
     */
    protected function __construct(
        string $username,
        string $email,
        string $plainPassword,
        UserPasswordEncoderInterface $encoder,
        array $roles = [],
        bool $enable = true
    ) {
        Assertion::email($email);
        $this->id = new Identity();
        $this->roles = $roles;
        $this->salt = $this->generateToken();
        $this->confirmationToken = $this->generateToken();
        $this->created = new DateTime();
        $this->username = $username;
        $this->email = $email;
        if ($enable) {
            $this->enable();
        }
        $this->updateCanonicalFields();
        $this->updatePassword($plainPassword, $encoder);
    }

    public static function register(
        $username,
        $email,
        $plainPassword,
        UserPasswordEncoderInterface $encoder,
        array $roles = ['ROLE_USER']
    ): User
    {
        $user = new static($username, $email, $plainPassword, $encoder, $roles);
        $user->recordUserRegistered();
        $user->enabled = true;

        return $user;
    }

    /**
     * Getter de id.
     *
     * @return Identity
     */
    public function getId(): Identity
    {
        return is_string($this->id) ? new Identity($this->id) : $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        //No-op
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired(): bool
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Change the email address of the user.
     *
     * @param string $email
     */
    public function changeEmail(string $email)
    {
        Assertion::email($email);
        $oldEmail = $this->email;
        $this->email = $email;
        $this->emailCanonical = self::canonicalize($this->email);
        $this->updated = new DateTime();
        $this->record(new EmailChanged($this->getId(), $oldEmail, $this->email));
    }

    /**
     * @param string                       $password
     * @param UserPasswordEncoderInterface $encoder
     */
    public function changePassword(string $password, UserPasswordEncoderInterface $encoder)
    {
        $oldPassword = $this->password;
        $this->updatePassword($password, $encoder);
        $this->updated = new DateTime();
        $this->record(new PasswordChanged($this->id, $oldPassword, $password));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enable
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     *
     * @return bool
     *
     */
    public function isEmailConfirmed(): bool
    {
        return null === $this->confirmationToken;
    }

    public function requestEmailConfirmation()
    {
        $this->confirmationToken = $this->generateToken();
        $this->updated = new DateTime();
        $this->record(new EmailValidationRequested($this->id, $this->confirmationToken));
    }

    public function confirmEmail(string $confirmationToken)
    {
        if ($this->isEmailConfirmed()) {
            throw new EmailAlreadyConfirmedException();
        }

        if ($confirmationToken !== $this->confirmationToken) {
            throw new InvalidEmailConfirmationTokenException();
        }

        $this->confirmationToken = null;
        $this->updated = new DateTime();
        $this->record(new EmailConfirmed($this->id, $this->email));
    }

    public function isPasswordResetRequested(): bool
    {
        return null !== $this->resettingToken;
    }

    public function requestPasswordReset()
    {
        $this->resettingToken = $this->generateToken();
        $this->passwordRequestedAt = new DateTime();
        $this->updated = new DateTime();
        $this->record(new PasswordResetRequested($this->id, $this->resettingToken));
    }

    public function resetPassword(string $resettingToken, string $password, UserPasswordEncoderInterface $encoder)
    {
        if (!$this->isPasswordResetRequested()) {
            throw new PasswordResettingNotRequestedException();
        }

        if ($resettingToken !== $this->resettingToken) {
            throw new InvalidPasswordResettingTokenException();
        }

        $this->resettingToken = null;

        $oldPassword = $this->password;
        $this->updatePassword($password, $encoder);
        $this->updated = new DateTime();
        $this->record(new PasswordReset($this->id, $oldPassword, $password));

        //We validate email if password has been confirmed because the token has been sent to the email adress.
        if (!$this->isEmailConfirmed()) {
            $this->confirmEmail($this->confirmationToken);
        }
    }

    /**
     * {@inheritdoc}
     */
    private function updateCanonicalFields()
    {
        $this->usernameCanonical = self::canonicalize($this->username);
        $this->emailCanonical = self::canonicalize($this->email);
    }

    /**
     * @param string                       $plainPassword
     * @param UserPasswordEncoderInterface $encoder
     */
    private function updatePassword(string $plainPassword, UserPasswordEncoderInterface $encoder)
    {
        $this->password = $encoder->encodePassword($this, $plainPassword);
    }

    /**
     * Generate token string for salt, email validation or password resetting.
     * @return string
     *
     */
    private function generateToken(): string
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    protected function recordUserRegistered()
    {
        $this->record(new UserRegistered($this->getId(), $this->getEmail(), $this->getUsername()));
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function canonicalize(string $string): string
    {
        $trimmed = trim($string);

        return mb_convert_case($trimmed, MB_CASE_LOWER, mb_detect_encoding($string));
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Getter de lastLogin
     *
     * @return DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Getter de confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Getter de resettingToken
     *
     * @return string
     */
    public function getResettingToken()
    {
        return $this->resettingToken;
    }

    /**
     * Getter de created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Getter de updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
