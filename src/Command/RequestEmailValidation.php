<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * Request an email validation process for a given user.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class RequestEmailValidation extends UserCommand
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
