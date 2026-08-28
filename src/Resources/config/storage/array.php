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

use Task\Storage\ArrayStorage\ArrayTaskExecutionRepository;
use Task\Storage\ArrayStorage\ArrayTaskRepository;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('task.storage.task', ArrayTaskRepository::class)
        ->public();

    $services->set('task.storage.task_execution', ArrayTaskExecutionRepository::class)
        ->public();
};
