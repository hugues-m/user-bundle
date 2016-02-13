<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Entity\Identity;

final class UserRegistered extends UserEvent
{
    /**
     * @var Identity
     */
    private $userId;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $username;

    public function __construct(Identity $userId, string $email, string $username)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->username = $username;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
