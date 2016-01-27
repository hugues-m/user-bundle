<?php

namespace HMLB\UserBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class UnusedUsername extends Constraint
{
    public $message = 'This username is already taken.';

    public function validatedBy()
    {
        return 'hmlb_user.unused_username';
    }
}
