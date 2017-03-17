<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Defines configuration for php-task library.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('task')
            ->children()
                ->enumNode('storage')->values(['array', 'doctrine'])->defaultValue('doctrine')->end()
                ->arrayNode('adapters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('doctrine')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('clear')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('run')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('mode')
                            ->values(['off', 'listener'])
                            ->defaultValue('off')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('system_tasks')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->scalarNode('handler_class')->end()
                            ->variableNode('workload')->defaultNull()->end()
                            ->scalarNode('cron_expression')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
