<?php

namespace src\Event;

use HMLB\DDD\Entity\Identity;
use HMLB\UserBundle\Event\UserEvent;

/**
 * PasswordResetRequested
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class PasswordResetRequested extends UserEvent
{
    /**
     * @var Identity
     */
    private $userId;

    /**
     * @var string
     */
    private $resetToken;

    public function __construct(Identity $userId, string $resetToken)
    {
        $this->userId = $userId;
        $this->resetToken = $resetToken;
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
