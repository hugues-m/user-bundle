<?php

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * ChangePassword.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class ChangePassword extends UserCommand
{
    /**
     * @var string
     */
    protected $password;

    /**
     * @var Identity
     */
    protected $user;

    public function __construct(string $password, Identity $user)
    {
        $this->password = $password;
        $this->user = $user;
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
    public function getUser(): Identity
    {
        return $this->user;
    }
}
