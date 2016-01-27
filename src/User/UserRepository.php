<?php

namespace HMLB\UserBundle\User;

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
     * @param string $username
     *
     * @return User
     *
     * @throws AggregateRootNotFoundException
     */
    public function getByUsername($username): User;
}
