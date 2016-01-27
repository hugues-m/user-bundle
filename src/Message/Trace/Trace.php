<?php

namespace HMLB\UserBundle\Message\Trace;

use DateTime;
use HMLB\UserBundle\User\User;

/**
 * Trace value object for more information about the context in which a message has been handled.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class Trace
{
    /**
     * @var DateTime
     */
    private $initiated;

    /**
     * @var Initiator
     */
    private $initiator;

    /**
     * Trace constructor.
     *
     * @param User|null $initiatingUser
     */
    public function __construct(User $initiatingUser = null)
    {
        $this->initiated = new DateTime();
        if ($initiatingUser) {
            $this->initiator = Initiator::fromUser($initiatingUser);
        }
    }

    /**
     * Getter de initiated.
     *
     * @return DateTime
     */
    public function getInitiated(): DateTime
    {
        return $this->initiated;
    }

    /**
     * @return bool
     */
    public function hasInitiator(): bool
    {
        return null !== $this->initiator;
    }

    /**
     * Getter de initiator.
     *
     * @return Initiator
     */
    public function getInitiator(): Initiator
    {
        return $this->initiator;
    }
}
