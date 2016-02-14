<?php

namespace spec\HMLB\UserBundle\Command;

use HMLB\DDD\Entity\Identity;
use HMLB\UserBundle\Command\ChangeEmail;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * ChangeEmailSpec
 *
 * @mixin ChangeEmail
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class ChangeEmailSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $id = new Identity();
        $this->beConstructedWith('my@email.com', $id);
        $this->getEmail()->shouldBe('my@email.com');
        $this->getUserId()->shouldBe($id);
        $this::messageName()->shouldBe('hmlb_user_change_email');
    }

}
