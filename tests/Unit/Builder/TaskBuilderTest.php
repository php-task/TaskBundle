<?php

namespace Task\TaskBundle\Unit\Builder;

use PHPUnit\Framework\TestCase;
use Task\Scheduler\TaskSchedulerInterface;
use Task\TaskBundle\Builder\NotSupportedMethodException;
use Task\TaskBundle\Builder\TaskBuilder;
use Task\TaskBundle\Entity\Task;
use Task\TaskInterface;

class TaskBuilderTest extends TestCase
{
    public function testSetSystemKey()
    {
        $task = $this->prophesize(Task::class);
        $scheduler = $this->prophesize(TaskSchedulerInterface::class);

        $taskBuilder = new TaskBuilder($task->reveal(), $scheduler->reveal());
        $taskBuilder->setSystemKey('test');

        $task->setSystemKey('test')->shouldBeCalled();
    }

    public function testSetSystemKeyNotSupported()
    {
        $this->expectException(NotSupportedMethodException::class);

        $task = $this->prophesize(TaskInterface::class);
        $scheduler = $this->prophesize(TaskSchedulerInterface::class);

        $taskBuilder = new TaskBuilder($task->reveal(), $scheduler->reveal());
        $taskBuilder->setSystemKey('test');
    }
}
