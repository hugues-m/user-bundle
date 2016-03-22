<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\MessageBus\Middleware;

use HMLB\DDD\Exception\AggregateRootNotFoundException;
use HMLB\DDD\Message\Message;
use HMLB\UserBundle\Message\Trace\Context;
use HMLB\UserBundle\Message\Trace\Trace;
use HMLB\UserBundle\Message\TraceableMessage;
use HMLB\UserBundle\User\UserRepository;
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

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
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

    /**
     * Adds a message trace to the message.
     *
     * @param TraceableMessage $message
     */
    private function traceMessage(TraceableMessage $message)
    {
        try {
            $user = $this->userRepository->getCurrentUser();
            $trace = Trace::user($user);
        } catch (AggregateRootNotFoundException $e) {
            if (Context::inCliContext()) {
                $trace = Trace::cli();
            } else {
                $trace = Trace::http();
            }
        }

        $message->trace($trace);
    }
}
