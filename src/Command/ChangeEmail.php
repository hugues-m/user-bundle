<?php

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * ChangeEmail.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class ChangeEmail extends UserCommand
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var Identity
     */
    protected $user;

    public function __construct(string $email, Identity $user)
    {
        $this->email = $email;
        $this->user = $user;
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
    public function getUser(): Identity
    {
        return $this->user;
    }
}
