<?php

namespace src\Command;

use HMLB\DDD\Entity\Identity;

/**
 * Reset a password after forgetting it.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class ResetPassword
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $resetToken;

    /**
     * @var Identity
     */
    private $userId;

    public function __construct(string $password, string $resetToken, Identity $userId)
    {
        $this->password = $password;
        $this->resetToken = $resetToken;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Getter de resetToken
     *
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
