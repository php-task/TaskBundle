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
                ->enumNode('storage')->values(['array', 'doctrine'])->defaultValue('array')->end()
            ->end();

        return $treeBuilder;
    }
}
