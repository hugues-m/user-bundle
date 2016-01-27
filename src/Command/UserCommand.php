<?php

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Message\Command\PersistentCommand;
use HMLB\UserBundle\Message\TraceableMessage;
use HMLB\UserBundle\Message\TraceableMessageCapabilities;

/**
 * UserCommand.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UserCommand extends PersistentCommand implements TraceableMessage
{
    use TraceableMessageCapabilities;
}
