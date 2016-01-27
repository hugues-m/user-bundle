<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Entity\Identity;

/**
 * UserDisabled.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UserDisabled extends UserEvent
{
    /**
     * @var Identity
     */
    protected $userId;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $username;

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
