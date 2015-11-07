<?php

namespace Task\TaskBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Container extension for php-task library.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class TaskExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('task.scheduler_service', $config['scheduler_service']);
        $container->setParameter('task.runner_service', $config['runner_service']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setAlias('task.scheduler', $container->getParameter('task.scheduler_service'));
        $container->setAlias('task.runner', $container->getParameter('task.runner_service'));
    }
}
