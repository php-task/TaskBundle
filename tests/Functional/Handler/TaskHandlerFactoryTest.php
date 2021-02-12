<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Functional\Handler;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\Handler\TaskHandlerNotExistsException;
use Task\TaskBundle\Handler\TaskHandlerFactory;
use Task\TaskBundle\Tests\Functional\TestHandler;

/**
 * Functional tests for task-handler definitions.
 */
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

    public function testCreate()
    {
        $this->assertInstanceOf(TestHandler::class, $this->taskHandlerFactory->create(TestHandler::class));
    }

    public function testCreateNotExists()
    {
        $this->expectException(TaskHandlerNotExistsException::class);

        $this->taskHandlerFactory->create(\stdClass::class);
    }
}
