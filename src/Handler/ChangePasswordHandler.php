<?php

namespace HMLB\UserBundle\Handler;

use HMLB\DDD\Message\Command\Command;
use HMLB\DDD\Message\Command\Handler;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;
use HMLB\UserBundle\Command\ChangePassword;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordHandler implements Handler
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
     * @param UserRepository               $userRepository
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * @param Command|ChangePassword $command
     */
    public function handle(Command $command)
    {
        /** @var User $user */
        $user = $this->userRepository->get($command->getUser());
        $user->changePassword($command->getPassword(), $this->encoder);
    }
}
