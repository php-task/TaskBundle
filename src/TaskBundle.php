<?php

namespace Task\TaskBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Task\TaskBundle\DependencyInjection\HandlerCompilerPass;
use Task\TaskBundle\DependencyInjection\TaskCompilerPass;

/**
 * Integrates php-task into symfony.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class TaskBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new HandlerCompilerPass());
        $container->addCompilerPass(new TaskCompilerPass());
    }
}
