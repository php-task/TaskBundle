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

use Task\Event\Events;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('task.events.create', Events::TASK_CREATE);
    $parameters->set('task.events.create_execution', Events::TASK_EXECUTION_CREATE);
    $parameters->set('task.events.before', Events::TASK_BEFORE);
    $parameters->set('task.events.after', Events::TASK_AFTER);
    $parameters->set('task.events.finished', Events::TASK_FINISHED);
    $parameters->set('task.events.passed', Events::TASK_PASSED);
    $parameters->set('task.events.failed', Events::TASK_FAILED);
    $parameters->set('task.events.retried', Events::TASK_RETRIED);
};
