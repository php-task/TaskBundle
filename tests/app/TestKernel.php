<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Kernel;
use Task\TaskBundle\TaskBundle;

class TestKernel extends Kernel
{
    const STORAGE_VAR_NAME = 'STORAGE';

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new TaskBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config.%s.yml', __DIR__, getenv(self::STORAGE_VAR_NAME)));
    }

    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');

        return $container;
    }
}

class TestHandler implements \Task\Handler\HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($workload)
    {
        return strrev($workload);
    }
}
