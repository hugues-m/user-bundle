<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Tests\Command;

use HMLB\DDD\Entity\Identity;
use HMLB\DDD\Persistence\PersistentMessage;
use HMLB\DDDBundle\Repository\PersistentCommandRepository;
use HMLB\UserBundle\Message\Trace\Context;
use HMLB\UserBundle\Message\Trace\Trace;
use HMLB\UserBundle\Message\TraceableCommand;
use SimpleBus\Message\Bus\MessageBus;

/**
 * Trait TraceableCommandTestingCapabilities.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
trait TraceableCommandTestingCapabilities
{
    /**
     * @param MessageBus                  $commandBus
     * @param PersistentMessage           $command
     * @param PersistentCommandRepository $commandRepository
     */
    protected function handleCommandAndAssertTraced(
        MessageBus $commandBus,
        PersistentMessage $command,
        PersistentCommandRepository $commandRepository
    ) {
        $commandBus->handle($command);

        $this->assertInstanceOf(Identity::class, $command->getId());

        $foundCommand = $commandRepository->get($command->getId());
        $this->assertSame($command, $foundCommand);

        if (!$command instanceof TraceableCommand) {
            $this->fail(sprintf('The message %s is not Traceable', get_class($command)));
        }
        $this->assertInstanceOf(Trace::class, $trace = $command->getTrace());
        $this->assertInstanceOf(Context::class, $trace->getContext());
    }
}
