<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Unit\Handler;

use Task\Handler\TaskHandlerInterface;
use Task\Handler\TaskHandlerNotExistsException;
use Task\TaskBundle\Handler\TaskHandlerFactory;

/**
 * Tests for class TaskHandlerFactory.
 */
class TaskHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $handler = new TestHandler();
        $taskHandlerFactory = new TaskHandlerFactory([TestHandler::class => $handler]);

        $this->assertEquals($handler, $taskHandlerFactory->create(TestHandler::class));
    }

    public function testCreateNotExists()
    {
        $this->setExpectedException(TaskHandlerNotExistsException::class);

        $taskHandlerFactory = new TaskHandlerFactory([TestHandler::class => new TestHandler()]);

        $taskHandlerFactory->create(\stdClass::class);
    }

    public function testCreateNoHandler()
    {
        $this->setExpectedException(TaskHandlerNotExistsException::class);

        $taskHandlerFactory = new TaskHandlerFactory([]);

        $taskHandlerFactory->create(\stdClass::class);
    }
}

class TestHandler implements TaskHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($workload)
    {
        return strrev($workload);
    }
}
