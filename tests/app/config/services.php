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

use Task\TaskBundle\Tests\Functional\FailTestHandler;
use Task\TaskBundle\Tests\Functional\TestHandler;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('test.handler', TestHandler::class)
        ->tag('task.handler', ['handler-name' => 'test']);

    $services->set('test.fail_handler', FailTestHandler::class)
        ->tag('task.handler', ['handler-name' => 'test']);
};
