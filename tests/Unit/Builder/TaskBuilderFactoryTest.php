<?php

namespace Unit\Builder;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Task\Scheduler\TaskSchedulerInterface;
use Task\TaskBundle\Builder\TaskBuilder;
use Task\TaskBundle\Builder\TaskBuilderFactory;
use Task\TaskInterface;

class TaskBuilderFactoryTest extends TestCase
{
    use ProphecyTrait;

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
