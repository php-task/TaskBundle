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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Task\Event\Events;
use Task\TaskBundle\EventListener\TaskExecutionListener;

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

        $this->loadDoctrineAdapter($config['adapters']['doctrine'], $container);
    }

    /**
     * Load doctrine adapter.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadDoctrineAdapter(array $config, ContainerBuilder $container)
    {
        if ($config['clear']) {
            $definition = new Definition(TaskExecutionListener::class, [new Reference('doctrine.orm.entity_manager', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)]);
            $definition->addTag(
                'kernel.event_listener',
                ['event' => Events::TASK_AFTER, 'method' => 'clearEntityManagerAfterTask']
            );
            $container->setDefinition('task.adapter.doctrine.execution_listener', $definition);
        }
    }
}
