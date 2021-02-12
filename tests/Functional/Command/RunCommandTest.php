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

        $execution = $this->taskExecutionRepository->findByUuid($executions[0]->getUuid());
        $this->assertEquals(TaskStatus::COMPLETED, $execution->getStatus(), $execution->getException() ?: '');
        $this->assertEquals(strrev('Test workload 1'), $execution->getResult());
        $this->assertGreaterThan(0, $execution->getDuration());
        $this->assertGreaterThanOrEqual($execution->getStartTime(), $execution->getEndTime());

        $execution = $this->taskExecutionRepository->findByUuid($executions[1]->getUuid());
        $this->assertEquals(TaskStatus::PLANNED, $execution->getStatus());
        $this->assertNull($execution->getResult());
        $this->assertNull($execution->getDuration());
        $this->assertNull($execution->getStartTime());
        $this->assertNull($execution->getEndTime());

        $execution = $this->taskExecutionRepository->findByUuid($executions[2]->getUuid());
        $this->assertEquals(TaskStatus::COMPLETED, $execution->getStatus());
        $this->assertEquals(strrev('Test workload 3'), $execution->getResult());
        $this->assertGreaterThan(0, $execution->getDuration());
        $this->assertGreaterThanOrEqual($execution->getStartTime(), $execution->getEndTime());

        $result = $this->taskExecutionRepository->findAll(2, 3);
        $this->assertCount(1, $result);

        $task = $result[0]->getTask();
        $this->assertEquals($intervalTask->getUuid(), $task->getUuid());
        $this->assertEquals($intervalTask->getHandlerClass(), $task->getHandlerClass());
        $this->assertEquals($intervalTask->getWorkload(), $task->getWorkload());
        $this->assertLessThanOrEqual($intervalTask->getFirstExecution(), $task->getFirstExecution());
        $this->assertLessThanOrEqual($intervalTask->getLastExecution(), $task->getLastExecution());
        $this->assertEquals($intervalTask->getInterval(), $task->getInterval());

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

        $execution = $this->taskExecutionRepository->findByUuid($executions[0]->getUuid());
        $this->assertEquals(TaskStatus::FAILED, $execution->getStatus());
        $this->assertNull($execution->getResult());
        $this->assertGreaterThan(0, $execution->getDuration());
        $this->assertGreaterThanOrEqual($execution->getStartTime(), $execution->getEndTime());

        $execution = $this->taskExecutionRepository->findByUuid($executions[1]->getUuid());
        $this->assertEquals(TaskStatus::PLANNED, $execution->getStatus());
        $this->assertNull($execution->getResult());
        $this->assertNull($execution->getDuration());
        $this->assertNull($execution->getStartTime());
        $this->assertNull($execution->getEndTime());

        $execution = $this->taskExecutionRepository->findByUuid($executions[2]->getUuid());
        $this->assertEquals(TaskStatus::FAILED, $execution->getStatus());
        $this->assertNull($execution->getResult());
        $this->assertGreaterThan(0, $execution->getDuration());
        $this->assertGreaterThanOrEqual($execution->getStartTime(), $execution->getEndTime());

        $result = $this->taskExecutionRepository->findAll(2, 3);
        $this->assertCount(1, $result);

        $task = $result[0]->getTask();
        $this->assertEquals($intervalTask->getUuid(), $task->getUuid());
        $this->assertEquals($intervalTask->getHandlerClass(), $task->getHandlerClass());
        $this->assertEquals($intervalTask->getWorkload(), $task->getWorkload());
        $this->assertLessThanOrEqual($intervalTask->getFirstExecution(), $task->getFirstExecution());
        $this->assertLessThanOrEqual($intervalTask->getLastExecution(), $task->getLastExecution());
        $this->assertEquals($intervalTask->getInterval(), $task->getInterval());

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
