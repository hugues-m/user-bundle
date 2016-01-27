<?php

namespace HMLB\UserBundle\User;

use DateTime;
use HMLB\DDD\Entity\AggregateRoot;
use HMLB\DDD\Entity\Identity;
use HMLB\DDD\Validation\Assertion;
use HMLB\UserBundle\Event\EmailChanged;
use HMLB\UserBundle\Event\PasswordChanged;
use HMLB\UserBundle\Event\UserRegistered;
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
     */
    protected function __construct(
        string $username,
        string $email,
        string $plainPassword,
        UserPasswordEncoderInterface $encoder,
        array $roles = []
    ) {
        Assertion::email($email);
        $this->id = new Identity();
        $this->roles = $roles;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->created = new DateTime();
        $this->username = $username;
        $this->email = $email;
        $this->updateCanonicalFields();
        $this->updatePassword($plainPassword, $encoder);
    }

    public static function register(
        $username,
        $email,
        $plainPassword,
        UserPasswordEncoderInterface $encoder,
        array $roles = ['ROLE_USER']
    ): self {
        $user = new self($username, $email, $plainPassword, $encoder, $roles);
        $user->recordUserRegistered();
        $user->enabled = true;

        return $user;
    }

    protected function recordUserRegistered()
    {
        $this->record(new UserRegistered($this->getId(), $this->getEmail(), $this->getUsername()));
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
    public function eraseCredentials(): self
    {
        //No-op

        return $this;
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
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable()
    {
        $this->enabled = true;
        $this->confirmationToken = null;

        return $this;
    }

    public function disable()
    {
        $this->enabled = false;

        return $this;
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
     * Change the email address of the user.
     *
     * @param string $email
     *
     * @return self
     */
    public function changeEmail(string $email): self
    {
        $oldEmail = $this->email;
        $this->email = $email;
        $this->emailCanonical = self::canonicalize($this->email);
        $this->updated = new DateTime();
        $this->record(new EmailChanged($this->getId(), $oldEmail, $this->email));

        return $this;
    }

    /**
     * @param string                       $password
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return self
     */
    public function changePassword(string $password, UserPasswordEncoderInterface $encoder): self
    {
        $oldPassword = $this->password;
        $this->updatePassword($password, $encoder);
        $this->updated = new DateTime();
        $this->record(new PasswordChanged($this->id, $oldPassword, $password));

        return $this;
    }

    /**
     * @param string                       $plainPassword
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return self
     */
    private function updatePassword(string $plainPassword, UserPasswordEncoderInterface $encoder)
    {
        $this->password = $encoder->encodePassword($this, $plainPassword);

        return $this;
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
}
