<?php

namespace HMLB\UserBundle\MessageBus\Middleware;

use HMLB\DDD\Message\Message;
use HMLB\UserBundle\Message\TraceableMessage;
use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

/**
 * Add trace information metadata on traceable messages.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class TracesMessages implements MessageBusMiddleware
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Message  $message
     * @param callable $next
     */
    public function handle($message, callable $next)
    {
        if ($message instanceof TraceableMessage) {
            $this->logger->debug(
                'TracesMessages traces '.$message::messageName()
            );
            $this->traceMessage($message);
        }
        $next($message);
    }

    private function traceMessage(TraceableMessage $message)
    {
        var_dump($_ENV);
    }
}
