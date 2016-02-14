<?php
declare (strict_types = 1);

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * ChangePassword.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
final class ChangePassword extends UserCommand
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var Identity
     */
    private $userId;

    public function __construct(string $password, Identity $userId)
    {
        $this->password = $password;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return Identity
     */
    public function getUserId(): Identity
    {
        return $this->userId;
    }
}
