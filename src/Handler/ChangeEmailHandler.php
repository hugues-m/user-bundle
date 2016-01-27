<?php

namespace HMLB\UserBundle\Handler;

use HMLB\DDD\Message\Command\Command;
use HMLB\DDD\Message\Command\Handler;
use HMLB\UserBundle\User\UserRepository;
use HMLB\UserBundle\Command\ChangeEmail;
use HMLB\UserBundle\User\User;

class ChangeEmailHandler implements Handler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Command|ChangeEmail $command
     */
    public function handle(Command $command)
    {
        /** @var User $user */
        $user = $this->userRepository->get($command->getUser());
        //No-op if email is same as existing
        if ($user->getEmail() == $command->getEmail()) {
            return;
        }

        $user->changeEmail($command->getEmail());
    }
}
