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

use Task\TaskBundle\Command\DebugTasksCommand;
use Task\TaskBundle\Command\ExecuteCommand;
use Task\TaskBundle\Command\RunCommand;
use Task\TaskBundle\Command\RunHandlerCommand;
use Task\TaskBundle\Command\ScheduleTaskCommand;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('task.command.run', RunCommand::class)
        ->public()
        ->args([
            null,
            service('task.runner'),
            service('task.scheduler'),
            // add entity_manager if doctrine storage is enabled
        ])
        ->tag('console.command', ['command' => 'task:run']);

    $services->set('task.command.run_handler', RunHandlerCommand::class)
        ->public()
        ->args([
            null,
            service('task.handler.factory'),
        ])
        ->tag('console.command', ['command' => 'task:run:handler']);

    $services->set('task.command.executor', ExecuteCommand::class)
        ->public()
        ->args([
            null,
            service('task.handler.factory'),
            service('task.storage.task_execution'),
            service('event_dispatcher'),
        ])
        ->tag('console.command', ['command' => 'task:execute']);

    $services->set('task.command.schedule_task', ScheduleTaskCommand::class)
        ->public()
        ->args([
            null,
            service('task.scheduler'),
            // add entity_manager if doctrine storage is enabled
        ])
        ->tag('console.command', ['command' => 'task:schedule']);

    $services->set('task.command.debug_tasks', DebugTasksCommand::class)
        ->public()
        ->args([
            null,
            service('task.storage.task_execution'),
        ])
        ->tag('console.command', ['command' => 'debug:tasks']);
};
