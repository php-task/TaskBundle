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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Container extension for php-task library.
 */
class TaskExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load(sprintf('storage/%s.xml', $config['storage']));
        $loader->load('task_event_listener.xml');
        $loader->load('scheduler.xml');
        $loader->load('command.xml');

        if ($config['run']['mode'] === 'listener') {
            $loader->load('listener.xml');
        }

        if ('doctrine' === $config['storage']) {
            // FIXME move to compiler pass
            $container->getDefinition('task.command.schedule_task')
                ->addArgument(new Reference('doctrine.orm.entity_manager'));
            $container->getDefinition('task.command.run')
                ->addArgument(new Reference('doctrine.orm.entity_manager'));
        }
    }
}
