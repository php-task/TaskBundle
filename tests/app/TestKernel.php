<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Kernel;
use Task\TaskBundle\TaskBundle;

class TestKernel extends Kernel
{
    const STORAGE_VAR_NAME = 'STORAGE';

    /**
     * @var string
     */
    private $storage;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TaskBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $this->storage = getenv(self::STORAGE_VAR_NAME);
        if ($this->storage === false) {
            $this->storage = 'array';
        }

        $loader->load(sprintf('%s/config/config.yml', __DIR__));
        $loader->load(sprintf('%s/config/config.%s.yml', __DIR__, $this->storage));
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.xml');

        $container->setParameter('kernel.storage', $this->storage);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeContainer()
    {
        $fresh = false;

        $container = $this->buildContainer();
        $container->compile();

        $this->container = $container;
        $this->container->set('kernel', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
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
