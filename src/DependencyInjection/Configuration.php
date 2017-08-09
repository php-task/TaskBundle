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
     * @var string[]
     */
    private $lockingStorageAliases = [];

    /**
     * @param \string[] $lockingStorageAliases
     */
    public function __construct(array $lockingStorageAliases)
    {
        $this->lockingStorageAliases = $lockingStorageAliases;
    }

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
                        ->enumNode('mode')->values(['off', 'listener'])->defaultValue('off')->end()
                    ->end()
                ->end()
                ->arrayNode('locking')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('storage')
                            ->values(array_keys($this->lockingStorageAliases))
                            ->defaultValue('file')
                        ->end()
                        ->integerNode('ttl')->defaultValue(600)->end()
                        ->arrayNode('storages')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('file')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('directory')->defaultValue('%kernel.cache_dir%/tasks')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('executor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('type')->values(['inline', 'process'])->defaultValue('inline')->end()
                        ->arrayNode('process')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('console')->defaultValue('%kernel.root_dir%/../bin/console')->end()
                            ->end()
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

    /**
     * Returns id for given storage-alias.
     *
     * @param string $alias
     *
     * @return string
     */
    public function getLockingStorageId($alias)
    {
        return $this->lockingStorageAliases[$alias];
    }
}
