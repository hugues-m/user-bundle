<?php

namespace HMLB\UserBundle\Message\Trace;

use HMLB\DDD\Entity\Identity;
use HMLB\UserBundle\User\User;

/**
 * Information on the User in the context of a message Trace.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class Initiator
{
    /**
     * @var Identity
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string|null
     */
    private $email;

    /**
     * Initiator constructor.
     *
     * @param Identity    $id
     * @param string      $username
     * @param null|string $email
     */
    private function __construct(Identity $id, string $username, string $email = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }

    /**
     * Instanciate an Initiator from a User instance.
     *
     * @param User $user
     *
     * @return Initiator
     */
    public static function fromUser(User $user): Initiator
    {
        $initiator = new self($user->getId(), $user->getUsername(), $user->getEmail());

        return $initiator;
    }

    /**
     * Getter de id.
     *
     * @return Identity
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter de username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Getter de email.
     *
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
