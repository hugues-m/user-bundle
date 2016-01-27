<?php

namespace spec\HMLB\UserBundle\User;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Specifications for the user object
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UserSpec extends ObjectBehavior
{

    function it_can_register(UserPasswordEncoder $encoder)
    {
        $encoder->encodePassword(Argument::type('HMLB\UserBundle\User\User'),'123456')->shouldBeCalled();
        $encoder->encodePassword(Argument::type('HMLB\UserBundle\User\User'),'123456')->willReturn('654321');

        $this->beConstructedThrough('register', ['test', 'test+oh@hmlb.fr', '123456', $encoder, ['ROLE_USER']]);

        $this->shouldHaveType('HMLB\UserBundle\User\User');
        $this->shouldHaveType('Symfony\Component\Security\Core\User\AdvancedUserInterface');

        $this->getUsername()->shouldBe('test');
        $this->getEmail()->shouldBe('test+oh@hmlb.fr');
        $this->getPassword()->shouldBe('654321');

        $this->recordedMessages()->shouldHaveCount(1);
        $this->getRoles()->shouldHaveCount(1);

        $this->recordedMessages()[0]->shouldHaveType('HMLB\UserBundle\Event\UserRegistered');
        $role =  $this->getRoles()[0];
        $role->shouldBe('ROLE_USER');
    }

    function it_canonicalizes_stuff(UserPasswordEncoder $encoder)
    {
        $this->beConstructedThrough('register', ['test', 'test+oh@hmlb.fr', '123456', $encoder, ['ROLE_USER']]);
        $this::canonicalize('je3JOiaA@Joa ear qdfSDç3RZàefzàF M')->shouldBe('je3joiaa@joa ear qdfsdç3rzàefzàf m');
    }
}
