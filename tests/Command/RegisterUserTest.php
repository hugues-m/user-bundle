<?php

namespace HMLB\UserBundle\Tests\Command;

use HMLB\DDDBundle\Repository\PersistentCommandRepository;
use HMLB\DDDBundle\Repository\PersistentEventRepository;
use HMLB\UserBundle\Command\RegisterUser;
use HMLB\UserBundle\Event\UserRegistered;
use HMLB\UserBundle\User\Role;

/**
 * RegisterUserTest.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class RegisterUserTest extends AbstractCommandTest
{
    /**
     * @test
     */
    public function commandIsHandled()
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
}
