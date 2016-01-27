<?php

namespace HMLB\UserBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use HMLB\DDD\Entity\Identity;
use HMLB\DDD\Exception\AggregateRootNotFoundException;
use HMLB\DDDBundle\Doctrine\ORM\AbstractORMRepository;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * DBALUserRepository.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class DBALUserRepository extends AbstractORMRepository implements UserRepository
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * The used User class.
     *
     * @var string
     */
    protected $class;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, $class)
    {
        $this->tokenStorage = $tokenStorage;
        $this->class = $class;
        parent::__construct($em);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            throw new AggregateRootNotFoundException();
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new AggregateRootNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getByUsername($username)
    {
        $user = $this->getOneBy(['username' => $username]);
        if (!$user instanceof User) {
            throw new AggregateRootNotFoundException();
        }

        return $user;
    }

    /**
     * @param Identity $identity
     *
     * @return User
     */
    public function get(Identity $identity)
    {
        return $this->entityRepository->find($identity);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->class;
    }
}
