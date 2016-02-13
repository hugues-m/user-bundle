<?php

namespace src\Command;

use HMLB\DDD\Entity\Identity;

/**
 * Request a password reset procedure after forgetting it.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class RequestPasswordReset
{
    /**
     * @var Identity
     */
    private $userId;

    public function __construct(Identity $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
