<?php

namespace HMLB\UserBundle\Tests\Command;

use HMLB\DDD\Entity\Identity;
use HMLB\DDDBundle\Repository\PersistentCommandRepository;
use HMLB\DDDBundle\Repository\PersistentEventRepository;
use HMLB\UserBundle\Command\RegisterUser;
use HMLB\UserBundle\Event\UserRegistered;
use HMLB\UserBundle\Message\Trace\Trace;
use HMLB\UserBundle\Message\Trace\Context;
use HMLB\UserBundle\Tests\Functional\TestKernel;
use HMLB\UserBundle\User\Role;
use PHPUnit_Framework_TestCase;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RegisterUserTest.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class RegisterUserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var TestKernel
     */
    private $kernel;

    public function setUp()
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $this->kernel = $kernel;
        $this->container = $kernel->getContainer();

        $this->executeCommand('doctrine:database:create');
        $this->executeCommand('doctrine:schema:update', ['--force' => true]);
    }

    public function tearDown()
    {
        $this->executeCommand('doctrine:database:drop', ['--force' => true]);
    }

    protected function executeCommand($command, array $options = [])
    {
        $args = [
            'command' => $command,
        ];
        //$args['--quiet'] = true;
        $args = array_merge($args, $options);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $application->run(new ArrayInput($args));
    }

    /**
     * @test
     */
    public function commandIsHandled()
    {
        $command = new RegisterUser('test', 'test@hmlb.fr', '123', [new Role('ROLE_USER')]);

        $this->getCommandBus()->handle($command);

        $this->assertInstanceOf(Identity::class, $command->getId());

        /* @var PersistentCommandRepository $repository */
        $commandRepository = $this->container->get('hmlb_ddd.repository.command');

        $foundCommand = $commandRepository->get($command->getId());
        $this->assertSame($command, $foundCommand);

        $foundCommands = $commandRepository->getByMessage(RegisterUser::class);
        $this->assertCount(1, $foundCommands);
        $this->assertSame($command, $foundCommands[0]);

        $this->assertInstanceOf(Trace::class, $trace = $command->getTrace());
        $this->assertInstanceOf(Context::class, $trace->getContext());

        //Assert trace...

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
     * @return MessageBus
     */
    private function getCommandBus(): MessageBus
    {
        return $this->container->get('command_bus');
    }
}
