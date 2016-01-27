<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Entity\Identity;

class PasswordChanged extends UserEvent
{
    /**
     * @var Identity
     */
    protected $userId;

    /**
     * @var string
     */
    protected $oldPassword;

    /**
     * @var string
     */
    protected $plainPassword;

    public function __construct(Identity $userId, string $oldPassword, string $plainPassword)
    {
        $this->userId = $userId;
        $this->oldPassword = $oldPassword;
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @return string
     */
    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
