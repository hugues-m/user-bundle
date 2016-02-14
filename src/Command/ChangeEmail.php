<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * ChangeEmail.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class ChangeEmail extends UserCommand
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var Identity
     */
    private $userId;

    public function __construct(string $email, Identity $userId)
    {
        $this->email = $email;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
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
