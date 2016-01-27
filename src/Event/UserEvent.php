<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Message\Event\PersistentEvent;
use HMLB\UserBundle\Message\TraceableMessage;
use HMLB\UserBundle\Message\TraceableMessageCapabilities;

/**
 * UserEvent.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UserEvent extends PersistentEvent implements TraceableMessage
{
    use TraceableMessageCapabilities;
}
