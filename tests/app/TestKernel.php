<?php

use Symfony\Component\Config\Loader\LoaderInterface;
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
}
