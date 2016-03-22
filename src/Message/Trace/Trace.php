<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Message\Trace;

use DateTime;
use HMLB\UserBundle\Exception\Exception;
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
     * @var Context
     */
    private $context;

    /**
     * Trace constructor.
     *
     * @param Context   $context
     * @param User|null $initiatingUser
     */
    private function __construct(Context $context, User $initiatingUser = null)
    {
        $this->context = $context;
        $this->initiated = new DateTime();
        if ($initiatingUser) {
            $this->initiator = Initiator::fromUser($initiatingUser);
        }
    }

    /**
     * Message trace when a message has been initiated by a domain user.
     *
     * @param User $initiatingUser
     *
     * @return Trace
     */
    public static function user(User $initiatingUser)
    {
        return new self(new Context(), $initiatingUser);
    }

    /**
     * Message trace when a message has been initiated by php CLI interface.
     *
     * @return Trace
     *
     * @throws Exception
     */
    public static function cli()
    {
        $context = new Context();
        if (!$context->isCli()) {
            throw new Exception('Not in php CLI context');
        }

        return new self($context);
    }

    /**
     * Message trace when a message has been initiated by php HTTP interface without domain user.
     *
     * @return Trace
     *
     * @throws Exception
     */
    public static function http()
    {
        $context = new Context();
        if ($context->isCli()) {
            throw new Exception('Not in php HTTP context');
        }

        return new self($context);
    }

    /**
     * Trace Context.
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Message initiation date.
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
