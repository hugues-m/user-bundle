<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Tests\Command;

use HMLB\DDDBundle\Repository\PersistentCommandRepository;
use HMLB\DDDBundle\Repository\PersistentEventRepository;
use HMLB\UserBundle\Command\ChangeEmail;
use HMLB\UserBundle\Command\ChangePassword;
use HMLB\UserBundle\Command\ConfirmEmail;
use HMLB\UserBundle\Command\RegisterUser;
use HMLB\UserBundle\Event\EmailChanged;
use HMLB\UserBundle\Event\EmailConfirmed;
use HMLB\UserBundle\Event\PasswordChanged;
use HMLB\UserBundle\Event\UserRegistered;
use HMLB\UserBundle\User\Role;
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

        /** @var PersistentCommandRepository $commandRepo */
        $commandRepo = $this->container->get('hmlb_ddd.repository.command');
        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $commandRepo
        );

        $foundCommands = $commandRepo->getByMessage(RegisterUser::class);
        $this->assertCount(1, $foundCommands);
        $this->assertSame($command, $foundCommands[0]);

        /** @var PersistentEventRepository $eventRepository */
        $eventRepository = $this->container->get('hmlb_ddd.repository.event');

        $foundEvents = $eventRepository->getByMessage(UserRegistered::class);
        $this->assertCount(1, $foundEvents);

        /** @var UserRegistered $event */
        $event = $foundEvents[0];
        $this->assertInstanceOf(UserRegistered::class, $event);
        $this->assertEquals($command->getUsername(), $event->getUsername());

        $foundEvent = $eventRepository->get($event->getId());
        $this->assertSame($event, $foundEvent);
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

        $commandRepo = $this->getCommandRepository();
        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $commandRepo
        );
        $endPwd = $user->getPassword();

        $this->assertNotEquals($beginningPwd, $endPwd);

        $eventRepository = $this->getEventRepository();

        $foundEvents = $eventRepository->getByMessage(PasswordChanged::class);
        $this->assertCount(1, $foundEvents);

        /** @var PasswordChanged $event */
        $event = $foundEvents[0];
        $this->assertInstanceOf(PasswordChanged::class, $event);
        $this->assertEquals($command->getUserId(), $event->getUserId());
        $this->assertNotEquals($event->getOldPassword(), $event->getNewPassword());

        $foundEvent = $eventRepository->get($event->getId());
        $this->assertSame($event, $foundEvent);
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

        $eventRepository = $this->getEventRepository();

        $foundEvents = $eventRepository->getByMessage(EmailChanged::class);
        $this->assertCount(1, $foundEvents);

        /** @var EmailChanged $event */
        $event = $foundEvents[0];
        $this->assertInstanceOf(EmailChanged::class, $event);
        $this->assertEquals($beforeEmail, $event->getOldEmail());
        $this->assertEquals($afterEmail, $event->getNewEmail());

        $foundEvent = $eventRepository->get($event->getId());
        $this->assertSame($event, $foundEvent);
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

        $commandRepo = $this->getCommandRepository();
        $this->handleCommandAndAssertTraced(
            $this->getCommandBus(),
            $command,
            $commandRepo
        );

        $this->assertTrue($user->isEmailConfirmed());

        $eventRepository = $this->getEventRepository();

        $foundEvents = $eventRepository->getByMessage(EmailConfirmed::class);
        $this->assertCount(1, $foundEvents);

        /** @var EmailConfirmed $event */
        $event = $foundEvents[0];
        $this->assertInstanceOf(EmailConfirmed::class, $event);
        $this->assertEquals($user->getId(), $event->getUserId());
        $this->assertEquals($user->getEmail(), $event->getEmail());

        $foundEvent = $eventRepository->get($event->getId());
        $this->assertSame($event, $foundEvent);
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
