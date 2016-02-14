<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Command;

use HMLB\DDD\Message\Command\PersistentCommand;
use HMLB\UserBundle\Message\TraceableCommand;
use HMLB\UserBundle\Message\TraceableMessageCapabilities;
use HMLB\UserBundle\Message\UserMessageNaming;

/**
 * UserCommand.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
abstract class UserCommand extends PersistentCommand implements TraceableCommand
{
    use TraceableMessageCapabilities;
    use UserMessageNaming;
}
