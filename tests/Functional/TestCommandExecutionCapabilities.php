<?php

namespace HMLB\UserBundle\Tests\Functional;

use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Trait TestCommandExecutionCapabilities.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
trait TestCommandExecutionCapabilities
{
    /**
     * @param       $command
     * @param array $options
     *
     * @throws Exception
     */
    protected function executeCommand($command, array $options = [])
    {
        $args = [
            'command' => $command,
        ];
        $args['--quiet'] = true;
        $args = array_merge($args, $options);

        $property = 'kernel';
        if (!property_exists($this, $property) || !$this->$property instanceof KernelInterface) {
            throw new Exception(
                sprintf(
                    'TestCommandExecutionCapabilities can only be used if the test case has a ::%s attribute.',
                    $property
                )
            );
        }
        $application = new Application($this->$property);
        $application->setAutoExit(false);
        $application->run(new ArrayInput($args));
    }
}
