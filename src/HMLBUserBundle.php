<?php

namespace HMLB\UserBundle;

use HMLB\UserBundle\DependencyInjection\HMLBUserExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * HMLBUserBundle.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class HMLBUserBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new HMLBUserExtension();
    }
}
