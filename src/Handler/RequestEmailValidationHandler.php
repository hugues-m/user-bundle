<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Handler;

use HMLB\DDD\Message\Command\Command;
use HMLB\DDD\Message\Command\Handler;
use HMLB\UserBundle\Command\RequestEmailValidation;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;

class RequestEmailValidationHandler implements Handler
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
     * @param Command|RequestEmailValidation $command
     */
    public function handle(Command $command)
    {
        /** @var User $user */
        $user = $this->userRepository->get($command->getUserId());
        $user->requestEmailConfirmation();
    }
}
