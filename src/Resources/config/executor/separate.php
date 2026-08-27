<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Task\TaskBundle\Executor\ExecutionProcessFactory;
use Task\TaskBundle\Executor\SeparateProcessExecutor;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('task.executor.separate', SeparateProcessExecutor::class)
        ->public()
        ->args([
            service('task.handler.factory'),
            service('task.storage.task_execution'),
            service('task.executor.separate.process_factory'),
        ]);

    $services->set('task.executor.separate.process_factory', ExecutionProcessFactory::class)
        ->public()
        ->args([
            '%task.executor.console_path%',
            '%task.executor.process_timeout%',
            '%kernel.environment%',
        ]);
};
