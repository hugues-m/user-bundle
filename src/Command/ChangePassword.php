<?php

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * ChangePassword.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class ChangePassword extends UserCommand
{
    /**
     * @var string
     */
    protected $password;

    /**
     * @var Identity
     */
    protected $userId;

    public function __construct(string $password, Identity $userId)
    {
        $this->password = $password;
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
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
