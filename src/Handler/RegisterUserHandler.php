<?php

namespace HMLB\UserBundle\Handler;

use HMLB\DDD\Message\Command\Command;
use HMLB\DDD\Message\Command\Handler;
use HMLB\UserBundle\Command\RegisterUser;
use HMLB\UserBundle\User\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * RegisterUserHandler.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class RegisterUserHandler implements Handler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var string
     */
    private $userClass;

    /**
     * RegisterUserHandler constructor.
     *
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param string                       $userClass
     */
    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        string $userClass
    ) {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
        $this->userClass = $userClass;
    }

    /**
     * @param Command|RegisterUser $command
     */
    public function handle(Command $command)
    {
        $user = call_user_func(
            [$this->userClass, 'register'],
            $command->getUsername(),
            $command->getEmail(),
            $command->getPassword(),
            $this->encoder
        );

        $this->userRepository->add($user);
    }
}
