<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Tests\Command;

use HMLB\DDD\Message\Event\PersistentEvent;
use HMLB\DDDBundle\Repository\PersistentCommandRepository;
use HMLB\DDDBundle\Repository\PersistentEventRepository;
use HMLB\UserBundle\Command\ChangeEmail;
use HMLB\UserBundle\Command\ChangePassword;
use HMLB\UserBundle\Command\ConfirmEmail;
use HMLB\UserBundle\Command\RegisterUser;
use HMLB\UserBundle\Command\RequestEmailValidation;
use HMLB\UserBundle\Event\EmailChanged;
use HMLB\UserBundle\Event\EmailConfirmed;
use HMLB\UserBundle\Event\EmailValidationRequested;
use HMLB\UserBundle\Event\PasswordChanged;
use HMLB\UserBundle\Event\UserRegistered;
use HMLB\UserBundle\User\Role;
use HMLB\UserBundle\User\User;
use HMLB\UserBundle\User\UserRepository;

/**
 * RegisterUserTest.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UserCommandsTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function usersCanRegister()
    {
        $command = new RegisterUser('test', 'test@hmlb.fr', '123', [new Role('ROLE_USER')]);

        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $this->getCommandRepository()
        );

        /** @var UserRegistered $event */
        $event = $this->getEvent(UserRegistered::class);
        $this->assertEquals($command->getUsername(), $event->getUsername());

        /** @var User $user */
        $user = $this->container->get('hmlb_user.repository.user')->get($event->getUserId());
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test', $user->getUsername());
        $this->assertEquals('test@hmlb.fr', $user->getEmail());
    }

    /**
     * @test
     */
    public function usersCanChangePassword()
    {
        $register = new RegisterUser('test', 'test@hmlb.fr', '123', [new Role('ROLE_USER')]);
        $this->getCommandBus()->handle($register);

        $user = $this->getUserRepository()->getByUsername($register->getUsername());
        $beginningPwd = $user->getPassword();
        $command = new ChangePassword('456', $user->getId());

        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $this->getCommandRepository()
        );
        $endPwd = $user->getPassword();

        $this->assertNotEquals($beginningPwd, $endPwd);

        /** @var PasswordChanged $event */
        $event = $this->getEvent(PasswordChanged::class);
        $this->assertEquals($command->getUserId(), $event->getUserId());
        $this->assertNotEquals($event->getOldPassword(), $event->getNewPassword());
    }

    /**
     * @test
     */
    public function usersCanChangeEmail()
    {
        $register = new RegisterUser('test', 'test@hmlb.fr', '123', [new Role('ROLE_USER')]);
        $this->getCommandBus()->handle($register);

        $user = $this->getUserRepository()->getByUsername($register->getUsername());
        $beforeEmail = $user->getEmail();
        $command = new ChangeEmail('test2@hmlb.fr', $user->getId());

        $commandRepo = $this->getCommandRepository();
        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $commandRepo
        );
        $afterEmail = $user->getEmail();

        $this->assertNotEquals($beforeEmail, $afterEmail);
        $this->assertEquals('test2@hmlb.fr', $afterEmail);

        /** @var EmailChanged $event */
        $event = $this->getEvent(EmailChanged::class);

        $this->assertEquals($beforeEmail, $event->getOldEmail());
        $this->assertEquals($afterEmail, $event->getNewEmail());
    }

    /**
     * @test
     */
    public function usersCanValidateEmail()
    {
        $register = new RegisterUser('test', 'test@hmlb.fr', '123', [new Role('ROLE_USER')]);
        $this->getCommandBus()->handle($register);

        $user = $this->getUserRepository()->getByUsername($register->getUsername());
        $this->assertFalse($user->isEmailConfirmed());
        $command = new ConfirmEmail($user->getConfirmationToken(), $user->getId());

        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $this->getCommandRepository()
        );

        $this->assertTrue($user->isEmailConfirmed());

        /** @var EmailConfirmed $event */
        $event = $this->getEvent(EmailConfirmed::class);
        $this->assertEquals($user->getId(), $event->getUserId());
        $this->assertEquals($user->getEmail(), $event->getEmail());
    }

    /**
     * @test
     */
    public function emailValidationCanBeRequested()
    {
        $register = new RegisterUser('test', 'test@hmlb.fr', '123', [new Role('ROLE_USER')]);
        $this->getCommandBus()->handle($register);

        $user = $this->getUserRepository()->getByUsername($register->getUsername());
        $confirm = new ConfirmEmail($user->getConfirmationToken(), $user->getId());
        $this->getCommandBus()->handle($confirm);
        $this->assertTrue($user->isEmailConfirmed());

        $command = new RequestEmailValidation($user->getId());

        $commandRepo = $this->getCommandRepository();
        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $commandRepo
        );

        $this->assertFalse($user->isEmailConfirmed());

        /** @var EmailValidationRequested $event */
        $event = $this->getEvent(EmailValidationRequested::class);
        $this->assertEquals($user->getId(), $event->getUserId());
        $this->assertEquals($user->getConfirmationToken(), $event->getValidationToken());
    }

    /**
     * @param $eventClassName
     *
     * @return PersistentEvent
     */
    private function getEvent($eventClassName)
    {
        $eventRepository = $this->getEventRepository();

        $foundEvents = $eventRepository->getByMessage($eventClassName);
        $this->assertCount(1, $foundEvents);

        $event = $foundEvents[0];
        $this->assertInstanceOf($eventClassName, $event);

        return $event;
    }

    private function getUserRepository(): UserRepository
    {
        return $this->container->get('hmlb_user.repository.user');
    }

    private function getCommandRepository(): PersistentCommandRepository
    {
        return $this->container->get('hmlb_ddd.repository.command');
    }

    private function getEventRepository(): PersistentEventRepository
    {
        return $this->container->get('hmlb_ddd.repository.event');
    }
}
