<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\Message;

/**
 * Trait UserMessageNaming.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
trait UserMessageNaming
{
    /**
     * @return string
     */
    protected static function generateMessageNameFromClassName(): string
    {
        $slices = explode('\\', get_called_class());

        return 'hmlb_user_'.strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', end($slices)));
    }
}
