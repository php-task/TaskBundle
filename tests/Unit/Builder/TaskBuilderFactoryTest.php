<?php

namespace Unit\Builder;

use Task\Scheduler\TaskSchedulerInterface;
use Task\TaskBundle\Builder\TaskBuilder;
use Task\TaskBundle\Builder\TaskBuilderFactory;
use Task\TaskInterface;

class TaskBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $task = $this->prophesize(TaskInterface::class);
        $scheduler = $this->prophesize(TaskSchedulerInterface::class);

        $taskBuilderFactory = new TaskBuilderFactory();

        $this->assertInstanceOf(
            TaskBuilder::class,
            $taskBuilderFactory->createTaskBuilder($task->reveal(), $scheduler->reveal())
        );
    }
}
