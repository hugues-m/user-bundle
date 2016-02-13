<?php

namespace HMLB\UserBundle\Event;

use HMLB\DDD\Message\Event\PersistentEvent;
use HMLB\UserBundle\Message\TraceableMessage;
use HMLB\UserBundle\Message\TraceableMessageCapabilities;
use HMLB\UserBundle\Message\UserMessageNaming;

/**
 * UserEvent.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
abstract class UserEvent extends PersistentEvent implements TraceableMessage
{
    use TraceableMessageCapabilities;
    use UserMessageNaming;
}
