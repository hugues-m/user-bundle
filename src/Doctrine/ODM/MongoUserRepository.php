<?php

namespace HMLB\UserBundle\Doctrine\ODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use HMLB\DDD\Entity\Identity;
use HMLB\DDD\Exception\AggregateRootNotFoundException;
use HMLB\DDDBundle\Doctrine\ODM\MongoDB\AbstractMongoRepository;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MongoUserRepository extends AbstractMongoRepository implements UserRepository
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

    public function __construct(DocumentManager $dm, TokenStorageInterface $tokenStorage, $class)
    {
        $this->tokenStorage = $tokenStorage;
        $this->class = $class;
        parent::__construct($dm);
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
     *
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    public function get(Identity $identity)
    {
        return $this->documentRepository->find((string) $identity);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->class;
    }
}
