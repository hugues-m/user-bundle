<?php

namespace HMLB\UserBundle\Security;

use HMLB\DDD\Exception\AggregateRootNotFoundException;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

/**
 * UserProvider.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        try {
            return $this->userRepository->getByUsername($username);
        } catch (AggregateRootNotFoundException $e) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf(
                    'Expected an instance of %s, but got "%s".',
                    User::class,
                    get_class($user)
                )
            );
        }

        /* @var User $user */
        try {
            $reloadedUser = $this->userRepository->get(
                $user->getId()
            );
        } catch (AggregateRootNotFoundException $e) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
