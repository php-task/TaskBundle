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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
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
    public function registerBundles(): array
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
    public function registerContainerConfiguration(LoaderInterface $loader): void
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
    protected function buildContainer(): ContainerBuilder
    {
        $container = parent::buildContainer();
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.php');

        $container->setParameter('kernel.storage', $this->storage);
        $container->setParameter('container.build_id', hash('crc32', 'Abc123423456789'));
        // Doctrine ORM requires either native lazy objects (PHP 8.4+) or the
        // (Symfony < 8) VarExporter-based lazy ghost implementation. Only enable
        // native lazy objects when running on a PHP version that supports them.
        $container->setParameter('task_test.native_lazy_objects', \PHP_VERSION_ID >= 80400);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeContainer(): void
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
        } catch (Exception $e) {
            $this->debug = $debug;

            throw $e;
        }

        $this->debug = $debug;
    }

    protected function getKernelParameters(): array
    {
        return array_merge(
            parent::getKernelParameters(),
            [
                'kernel.test_root_dir' => __DIR__,
            ]
        );
    }
}
