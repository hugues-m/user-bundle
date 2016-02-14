<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Entity\Identity;

/**
 * EmailConfirmed
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class EmailConfirmed extends UserEvent
{
    /**
     * @var Identity
     */
    private $userId;

    /**
     * @var string
     */
    private $email;

    public function __construct(Identity $userId, string $email)
    {
        $this->userId = $userId;
        $this->email = $email;
    }

    /**
     * Getter de email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
