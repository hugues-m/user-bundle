<?php
declare (strict_types = 1);

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * Confirm that an email adress is owned by the User.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class ConfirmEmail extends UserCommand
{
    /**
     * @var string
     */
    private $confirmationToken;

    /**
     * @var Identity
     */
    private $userId;

    public function __construct(string $confirmationToken, Identity $userId)
    {
        $this->confirmationToken = $confirmationToken;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
