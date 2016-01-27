<?php

namespace HMLB\UserBundle\DependencyInjection;

use HMLB\UserBundle\User\User;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link * http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
                        return !$class instanceof User;
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
