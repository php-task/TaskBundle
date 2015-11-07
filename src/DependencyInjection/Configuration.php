<?php

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
                ->scalarNode('scheduler_service')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('runner_service')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
