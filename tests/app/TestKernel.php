<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
        if (false === $this->storage) {
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
        $container->setParameter('container.build_id', hash('crc32', 'Abc123423456789'));

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeContainer()
    {
        static $first = true;

        if ('test' !== $this->getEnvironment()) {
            parent::initializeContainer();

            return;
        }

        $debug = $this->debug;

        if (!$first) {
            // disable debug mode on all but the first initialization
            $this->debug = false;
        }

        // will not work with --process-isolation
        $first = false;

        try {
            parent::initializeContainer();
        } catch (\Exception $e) {
            $this->debug = $debug;

            throw $e;
        }

        $this->debug = $debug;
    }

    protected function getKernelParameters()
    {
        return array_merge(
            parent::getKernelParameters(),
            [
                'kernel.test_root_dir' => __DIR__,
            ]
        );
    }
}
