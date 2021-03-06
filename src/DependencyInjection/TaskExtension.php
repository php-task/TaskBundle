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

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Task\Event\Events;
use Task\TaskBundle\EventListener\DoctrineTaskExecutionListener;

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
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('task.system_tasks', $config['system_tasks']);

        $container->setAlias('task.lock.storage', $configuration->getLockingStorageId($config['locking']['storage']));
        foreach (array_keys($config['locking']['storages']) as $key) {
            $container->setParameter('task.lock.storages.' . $key, $config['locking']['storages'][$key]);
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load(sprintf('storage/%s.xml', $config['storage']));
        $loader->load('task_event_listener.xml');
        $loader->load('scheduler.xml');
        $loader->load('command.xml');
        $loader->load('locking/services.xml');

        if ($config['run']['mode'] === 'listener') {
            $loader->load('listener.xml');
        }

        $this->loadDoctrineAdapter($config['adapters']['doctrine'], $container);
        $this->loadLockingComponent($config['locking'], $container, $loader);
        $this->loadExecutorComponent($config['executor'], $container, $loader);
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
            $definition = new Definition(
                DoctrineTaskExecutionListener::class,
                [new Reference('doctrine.orm.entity_manager', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)]
            );
            $definition->addTag(
                'kernel.event_listener',
                ['event' => Events::TASK_AFTER, 'method' => 'clearEntityManagerAfterTask']
            );
            $container->setDefinition('task.adapter.doctrine.execution_listener', $definition);
        }
    }

    /**
     * Load services for locking component.
     *
     * @param array $config
     * @param LoaderInterface $loader
     * @param ContainerBuilder $container
     */
    private function loadLockingComponent(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (!$config['enabled'] || 'null' === $config['storage']) {
            return $loader->load('locking/null.xml');
        }

        $loader->load('locking/services.xml');
        $container->setParameter('task.lock.ttl', $config['ttl']);
    }

    /**
     * Load services for executor component.
     *
     * @param array $config
     * @param LoaderInterface $loader
     * @param ContainerBuilder $container
     */
    private function loadExecutorComponent(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        $loader->load('executor/' . $config['type'] . '.xml');
        $container->setAlias('task.executor', 'task.executor.' . $config['type']);

        if (!array_key_exists($config['type'], $config)) {
            return;
        }

        foreach ($config[$config['type']] as $key => $value) {
            $container->setParameter('task.executor.' . $key, $value);
        }

        if (!file_exists($container->getParameter('task.executor.console_path'))) {
            throw new InvalidConfigurationException(
                'Console file does not exists! Given in "task.executor.seperate.console".'
            );
        }
    }

    /**
     * Find storage aliases and related ids.
     *
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getLockingStorageAliases(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('task.lock.storage');

        $result = ['null'];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $result[$tag['alias']] = $id;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('locking/storages.xml');

        return new Configuration($this->getLockingStorageAliases($container));
    }
}
