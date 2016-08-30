<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\TaskBundle\Handler\TaskHandlerFactory;

class TaskHandlerFactoryTest extends KernelTestCase
{
    /**
     * @var TaskHandlerFactory
     */
    private $taskHandlerFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();
        $this->taskHandlerFactory = self::$kernel->getContainer()->get('task.handler.factory');
    }

    public function testRun()
    {
        $this->assertInstanceOf(\TestHandler::class, $this->taskHandlerFactory->create(\TestHandler::class));
    }
}
