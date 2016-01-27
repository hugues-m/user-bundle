<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Entity\Identity;

class EmailChanged extends UserEvent
{
    /**
     * @var Identity
     */
    protected $userId;
    /**
     * @var string
     */
    protected $oldEmail;

    /**
     * @var string
     */
    protected $newEmail;

    public function __construct(Identity $userId, string $oldEmail, string $newEmail)
    {
        $this->userId = $userId;
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }

    /**
     * @return string
     */
    public function getNewEmail(): string
    {
        return $this->newEmail;
    }

    /**
     * @return string
     */
    public function getOldEmail(): string
    {
        return $this->oldEmail;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
