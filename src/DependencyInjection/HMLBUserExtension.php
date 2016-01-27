<?php

namespace HMLB\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HMLBUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * We add mapping information for our Messages Classes.
     *
     * todo: It should be dynamic for non default entity_manager name
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $mappingConfig = [
                'orm' => [
                    'entity_managers' => [
                        'default' => [
                            'mappings' => [
                                'HMLBUserBundle' => [
                                    'mapping' => true,
                                    'type' => 'xml',
                                    'dir' => __DIR__.'/../Resources/config/doctrine',
                                    'prefix' => 'HMLB\UserBundle',
                                    'is_bundle' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $container->getExtension('doctrine');
            $container->prependExtensionConfig('doctrine', $mappingConfig);
        }
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->remapParametersNamespaces(
            $config,
            $container,
            [
                '' => [
                    'user_class' => 'hmlb_user.user_class',
                ],
            ]
        );

        return $config;
    }

    private function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    private function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    public function getAlias()
    {
        return 'hmlb_user';
    }
}
