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

use Task\TaskBundle\Command\ScheduleSystemTasksCommand;
use Task\TaskBundle\Entity\Task;
use Task\TaskBundle\Entity\TaskExecution;
use Task\TaskBundle\Entity\TaskExecutionRepository;
use Task\TaskBundle\Entity\TaskRepository;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('task.repository.task', TaskRepository::class)
        ->public()
        ->factory([service('doctrine.orm.entity_manager'), 'getRepository'])
        ->args([Task::class]);

    $services->alias('task.storage.task', 'task.repository.task')
        ->public();

    $services->set('task.repository.task_execution', TaskExecutionRepository::class)
        ->public()
        ->factory([service('doctrine.orm.entity_manager'), 'getRepository'])
        ->args([TaskExecution::class]);

    $services->alias('task.storage.task_execution', 'task.repository.task_execution')
        ->public();

    $services->set('task.command.schedule_system_tasks', ScheduleSystemTasksCommand::class)
        ->public()
        ->args([
            null,
            '%task.system_tasks%',
            service('task.scheduler'),
            service('task.repository.task'),
            service('task.storage.task_execution'),
        ])
        ->tag('console.command', ['command' => 'task:schedule:system-tasks']);
};
