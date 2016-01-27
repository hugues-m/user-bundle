<?php

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;

/**
 * RegisterUser.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class RegisterUser extends UserCommand
{
    /**
     * Identity of the registered user.
     *
     * @var Identity
     */
    private $userIdentity;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $roles;

    /**
     * RegisterUser constructor.
     *
     * @param string $username
     * @param string $email
     * @param array  $roles
     * @param string $password
     */
    public function __construct(string $username, string $email, string $password, array $roles)
    {
        $this->username = $username;
        $this->email = $email;
        $this->roles = $roles;
        $this->password = $password;
        $this->userIdentity = new Identity();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
