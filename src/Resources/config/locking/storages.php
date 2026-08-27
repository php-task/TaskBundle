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

use Task\Lock\Storage\FileLockStorage;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('task.lock.storage.file', FileLockStorage::class)
        ->public()
        ->args([expr("parameter('task.lock.storages.file')['directory']")])
        ->tag('task.lock.storage', ['alias' => 'file']);
};
