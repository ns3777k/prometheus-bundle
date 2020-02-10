<?php

declare(strict_types=1);

namespace Ns3777k\PrometheusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ns3777k_prometheus');
        $rootNode = $treeBuilder->getRootNode();
        $supportedTypes = ['in_memory', 'apcu', 'redis'];

        $rootNode
            ->children()
                ->scalarNode('namespace')
                    ->defaultValue('')
                    ->example('my_app')
                    ->info('Metrics prefix')
                ->end()
                ->arrayNode('listener')
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('enabled')
                                ->defaultTrue()
                            ->end()
                            ->arrayNode('ignored_routes')
                                ->arrayPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('redis')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('host')
                                ->defaultValue('127.0.0.1')
                            ->end()
                            ->integerNode('port')
                                ->defaultValue(6379)
                            ->end()
                            ->floatNode('timeout')
                                ->defaultValue(0.1)
                            ->end()
                            ->floatNode('read_timeout')
                                ->defaultValue(10)
                            ->end()
                            ->booleanNode('persistent_connections')
                                ->defaultFalse()
                            ->end()
                            ->scalarNode('password')
                            ->end()
                            ->integerNode('database')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('adapter')
                    ->validate()
                        ->ifNotInArray($supportedTypes)
                        ->thenInvalid('The type %s is not supported. Please choose one of '.json_encode($supportedTypes))
                    ->end()
                    ->defaultValue('in_memory')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
