<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Handler;

use HMLB\DDD\Message\Command\Command;
use HMLB\DDD\Message\Command\Handler;
use HMLB\UserBundle\Command\ResetPassword;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordHandler implements Handler
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
     * @param Command|ResetPassword $command
     */
    public function handle(Command $command)
    {
        /** @var User $user */
        $user = $this->userRepository->get($command->getUserId());
        $user->resetPassword($command->getResetToken(), $command->getPassword(), $this->encoder);
    }
}
