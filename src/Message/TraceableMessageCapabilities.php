<?php

namespace HMLB\UserBundle\Message;

use HMLB\UserBundle\Exception\MissingTraceException;
use HMLB\UserBundle\Message\Trace\Trace;

/**
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
trait TraceableMessageCapabilities
{
    /**
     * @var Trace
     */
    private $messageTrace;

    /**
     * {@inheritdoc}
     */
    public function trace(Trace $trace)
    {
        $this->messageTrace = $trace;
    }

    /**
     * @return Trace
     *
     * @throws MissingTraceException If the message have not been Traced Yet.
     */
    public function getTrace(): Trace
    {
        if (null === $this->messageTrace) {
            throw new MissingTraceException();
        }

        return $this->messageTrace;
    }
}
