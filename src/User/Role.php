<?php

namespace HMLB\UserBundle\User;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Security Role.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class Role implements RoleInterface
{
    /**
     * @var string
     */
    private $role;

    /**
     * @param string $role
     */
    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * The role.
     *
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->role;
    }
}
