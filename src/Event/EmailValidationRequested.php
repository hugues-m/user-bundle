<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Entity\Identity;

/**
 * EmailValidationRequested
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class EmailValidationRequested extends UserEvent
{
    /**
     * @var Identity
     */
    private $userId;

    /**
     * @var string
     */
    private $validationToken;

    public function __construct(Identity $userId, string $validationToken)
    {
        $this->userId = $userId;
        $this->validationToken = $validationToken;
    }

    /**
     * Getter de validationToken
     *
     * @return string
     */
    public function getValidationToken()
    {
        return $this->validationToken;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
