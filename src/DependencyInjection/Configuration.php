<?php

declare (strict_types = 1);

namespace HMLB\UserBundle\DependencyInjection;

use HMLB\UserBundle\User\User;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * UserBundle Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hmlb_user');

        $rootNode
            ->children()
            ->scalarNode('user_class')
            ->defaultValue(User::class)
            ->validate()
            ->ifTrue(
                function ($class): bool
                {
                    if ($class) {
                        return !is_subclass_of($class, User::class);
                    }

                    return false;
                }
            )
            ->thenInvalid('The HMLBUserBundle user class must extend '.User::class)
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
