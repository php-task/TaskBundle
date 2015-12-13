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

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load(sprintf('storage/%s.xml', $config['storage']));
        $loader->load('scheduler.xml');
        $loader->load('command.xml');

        if ($config['run']['mode'] === 'listener') {
            $loader->load('listener.xml');
        }
    }
}
