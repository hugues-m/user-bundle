<?php

namespace HMLB\UserBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Trait TestCommandExecutionCapabilities.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
trait TestCommandExecutionCapabilities
{
    protected function executeCommand($command, array $options = [])
    {
        $args = [
            'command' => $command,
        ];
        $args['--quiet'] = true;
        $args = array_merge($args, $options);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $application->run(new ArrayInput($args));
    }
}
