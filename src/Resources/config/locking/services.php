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

use Task\Lock\Lock;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('task.lock', Lock::class)
        ->public()
        ->args([
            service('task.lock.storage'),
            '%task.lock.ttl%',
        ]);
};
