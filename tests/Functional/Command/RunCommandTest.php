<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Functional\Command;

use Cron\CronExpression;
use Task\Execution\TaskExecutionInterface;
use Task\TaskBundle\Tests\Functional\BaseCommandTestCase;
use Task\TaskBundle\Tests\Functional\FailTestHandler;
use Task\TaskBundle\Tests\Functional\TestHandler;
use Task\TaskStatus;

/**
 * Tests for RunCommand.
 */
class RunCommandTest extends BaseCommandTestCase
{
    public function testExecute()
    {
        $singleTask = $this->createTask('Test workload 1');
        $laterTask = $this->createTask('Test workload 2');
        $intervalTask = $this->createTask('Test workload 3', CronExpression::factory('@daily'));

        /** @var TaskExecutionInterface[] $executions */
        $executions = [
            $this->createTaskExecution($singleTask, new \DateTime('-1 hour')),
            $this->createTaskExecution($laterTask, new \DateTime('+1 hour')),
            $this->createTaskExecution($intervalTask, new \DateTime('-2 hour')),
        ];

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
            ]
        );

        $this->assertEquals(TaskStatus::COMPLETED, $executions[0]->getStatus());
        $this->assertEquals(strrev('Test workload 1'), $executions[0]->getResult());
        $this->assertGreaterThan(0, $executions[0]->getDuration());
        $this->assertGreaterThanOrEqual($executions[0]->getStartTime(), $executions[0]->getEndTime());

        $this->assertEquals(TaskStatus::PLANNED, $executions[1]->getStatus());
        $this->assertNull($executions[1]->getResult());
        $this->assertNull($executions[1]->getDuration());
        $this->assertNull($executions[1]->getStartTime());
        $this->assertNull($executions[1]->getEndTime());

        $this->assertEquals(TaskStatus::COMPLETED, $executions[2]->getStatus());
        $this->assertEquals(strrev('Test workload 3'), $executions[2]->getResult());
        $this->assertGreaterThan(0, $executions[2]->getDuration());
        $this->assertGreaterThanOrEqual($executions[2]->getStartTime(), $executions[2]->getEndTime());

        $result = $this->taskExecutionRepository->findAll(2, 3);
        $this->assertCount(1, $result);

        $this->assertEquals($intervalTask, $result[0]->getTask());
        $this->assertEquals(TaskStatus::PLANNED, $result[0]->getStatus());
        $this->assertEquals(TestHandler::class, $result[0]->getHandlerClass());
        $this->assertEquals('Test workload 3', $result[0]->getWorkload());
    }

    public function testExecuteWithFail()
    {
        $singleTask = $this->createTask('Test workload 1', null, FailTestHandler::class);
        $laterTask = $this->createTask('Test workload 2', null, FailTestHandler::class);
        $intervalTask = $this->createTask('Test workload 3', CronExpression::factory('@daily'), FailTestHandler::class);

        /** @var TaskExecutionInterface[] $executions */
        $executions = [
            $this->createTaskExecution($singleTask, new \DateTime('-1 hour')),
            $this->createTaskExecution($laterTask, new \DateTime('+1 hour')),
            $this->createTaskExecution($intervalTask, new \DateTime('-2 hour')),
        ];

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
            ]
        );

        $this->assertEquals(TaskStatus::FAILED, $executions[0]->getStatus());
        $this->assertNull($executions[0]->getResult());
        $this->assertGreaterThan(0, $executions[0]->getDuration());
        $this->assertGreaterThanOrEqual($executions[0]->getStartTime(), $executions[0]->getEndTime());

        $this->assertEquals(TaskStatus::PLANNED, $executions[1]->getStatus());
        $this->assertNull($executions[1]->getResult());
        $this->assertNull($executions[1]->getDuration());
        $this->assertNull($executions[1]->getStartTime());
        $this->assertNull($executions[1]->getEndTime());

        $this->assertEquals(TaskStatus::FAILED, $executions[2]->getStatus());
        $this->assertNull($executions[2]->getResult());
        $this->assertGreaterThan(0, $executions[2]->getDuration());
        $this->assertGreaterThanOrEqual($executions[2]->getStartTime(), $executions[2]->getEndTime());

        $result = $this->taskExecutionRepository->findAll(2, 3);
        $this->assertCount(1, $result);

        $this->assertEquals($intervalTask, $result[0]->getTask());
        $this->assertEquals(TaskStatus::PLANNED, $result[0]->getStatus());
        $this->assertEquals(FailTestHandler::class, $result[0]->getHandlerClass());
        $this->assertEquals('Test workload 3', $result[0]->getWorkload());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommand()
    {
        return self::$kernel->getContainer()->get('task.command.run');
    }
}
