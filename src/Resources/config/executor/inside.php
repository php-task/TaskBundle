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

use Task\Executor\InsideProcessExecutor;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('task.executor.inside', InsideProcessExecutor::class)
        ->public()
        ->args([service('task.handler.factory')]);
};
