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

use Task\Runner\PendingExecutionFinder;
use Task\Runner\TaskRunner;
use Task\Scheduler\TaskScheduler;
use Task\TaskBundle\Builder\TaskBuilderFactory;
use Task\TaskBundle\Handler\TaskHandlerFactory;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('task.builder_factory', TaskBuilderFactory::class)
        ->public();

    $services->set('task.scheduler', TaskScheduler::class)
        ->public()
        ->args([
            service('task.builder_factory'),
            service('task.storage.task'),
            service('task.storage.task_execution'),
            service('event_dispatcher'),
        ]);

    $services->set('task.handler.factory', TaskHandlerFactory::class)
        ->public()
        ->args([[]]);

    $services->set('task.runner.execution_finder', PendingExecutionFinder::class)
        ->public()
        ->args([
            service('task.storage.task_execution'),
            service('task.handler.factory'),
            service('task.lock'),
            service('logger')->ignoreOnInvalid(),
        ]);

    $services->set('task.runner', TaskRunner::class)
        ->public()
        ->args([
            service('task.storage.task_execution'),
            service('task.runner.execution_finder'),
            service('task.executor'),
            service('event_dispatcher'),
        ]);
};
