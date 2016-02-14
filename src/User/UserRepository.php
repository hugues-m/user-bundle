<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\User;

use HMLB\DDD\Entity\Identity;
use HMLB\DDD\Entity\Repository;
use HMLB\DDD\Exception\AggregateRootNotFoundException;

/**
 * Interface UserRepository.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
interface UserRepository extends Repository
{
    /**
     * @return User
     *
     * @throws AggregateRootNotFoundException
     */
    public function getCurrentUser(): User;

    /**
     * @param Identity $identity
     *
     * @return User
     */
    public function get(Identity $identity): User;

    /**
     * @param string $username
     *
     * @return User
     *
     * @throws AggregateRootNotFoundException
     */
    public function getByUsername($username): User;
}
