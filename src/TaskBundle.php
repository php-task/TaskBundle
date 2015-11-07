<?php

namespace Task\TaskBundle;

use DependencyInjection\WorkerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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

        $container->addCompilerPass(new WorkerCompilerPass());
    }

}
