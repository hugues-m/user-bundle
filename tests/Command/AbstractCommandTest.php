<?php

namespace HMLB\UserBundle\Tests\Command;

use HMLB\UserBundle\Tests\Functional\TestCommandExecutionCapabilities;
use HMLB\UserBundle\Tests\Functional\TestKernel;
use PHPUnit_Framework_TestCase;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Helper class for testing command handling.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
abstract class AbstractCommandTest extends PHPUnit_Framework_TestCase
{
    use TraceableCommandTestingCapabilities;
    use TestCommandExecutionCapabilities;

    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var TestKernel
     */
    protected $kernel;

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

    /**
     * @return MessageBus
     */
    protected function getCommandBus(): MessageBus
    {
        return $this->container->get('command_bus');
    }
}
