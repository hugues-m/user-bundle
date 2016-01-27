<?php

namespace HMLB\UserBundle\Message;

use HMLB\UserBundle\Exception\MissingTraceException;
use HMLB\UserBundle\Message\Trace\Trace;

/**
 * Initiator of this kind of message is traceable.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
interface TraceableMessage
{
    /**
     * Add Trace metadata to the message.
     *
     * @param Trace $trace
     */
    public function trace(Trace $trace);

    /**
     * @return Trace
     *
     * @throws MissingTraceException If the message have not been Traced Yet.
     */
    public function getTrace(): Trace;
}
