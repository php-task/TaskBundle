<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Functional\Command;

use Task\TaskBundle\Tests\Functional\BaseCommandTestCase;
use Task\TaskBundle\Tests\Functional\TestHandler;

/**
 * Tests for ScheduleTaskCommand.
 */
class ScheduleTaskCommandTest extends BaseCommandTestCase
{
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
            ]
        );

        $tasks = $this->taskRepository->findAll();
        $this->assertCount(1, $tasks);

        $this->assertEquals(TestHandler::class, $tasks[0]->getHandlerClass());

        $executions = $this->taskExecutionRepository->findAll();
        $this->assertCount(1, $executions);

        $this->assertEquals(TestHandler::class, $executions[0]->getHandlerClass());
    }

    public function testExecuteWithWorkload()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
                'workload' => 'Test workload 1',
            ]
        );

        $tasks = $this->taskRepository->findAll();
        $this->assertCount(1, $tasks);

        $this->assertEquals(TestHandler::class, $tasks[0]->getHandlerClass());
        $this->assertEquals('Test workload 1', $tasks[0]->getWorkload());
    }

    public function testExecuteWithWorkloadAndInterval()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
                'workload' => 'Test workload 1',
                '--cron-expression' => '0 * * * *',
            ]
        );

        $tasks = $this->taskRepository->findAll();
        $this->assertCount(1, $tasks);

        $this->assertEquals(TestHandler::class, $tasks[0]->getHandlerClass());
        $this->assertEquals('Test workload 1', $tasks[0]->getWorkload());
        $this->assertEquals('0 * * * *', $tasks[0]->getInterval());
    }

    public function testExecuteWithWorkloadAndIntervalAndEndDate()
    {
        $date = new \DateTime('+1 day');
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
                'workload' => 'Test workload 1',
                '--cron-expression' => '0 * * * *',
                '--end-date' => $date->format(\DateTime::RFC3339),
            ]
        );

        $tasks = $this->taskRepository->findAll();
        $this->assertCount(1, $tasks);

        $this->assertEquals(TestHandler::class, $tasks[0]->getHandlerClass());
        $this->assertEquals('Test workload 1', $tasks[0]->getWorkload());
        $this->assertEquals('0 * * * *', $tasks[0]->getInterval());
        $this->assertEquals($date->format('Y-m-d H:i:s'), $tasks[0]->getLastExecution()->format('Y-m-d H:i:s'));
    }

    public function testExecuteWithExecutionDate()
    {
        $date = new \DateTime('+1 day');
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
                'workload' => 'Test workload 1',
                '--execution-date' => '+1 day',
            ]
        );

        $tasks = $this->taskRepository->findAll();
        $this->assertCount(1, $tasks);

        $this->assertEquals(TestHandler::class, $tasks[0]->getHandlerClass());
        $this->assertGreaterThanOrEqual($date, $tasks[0]->getFirstExecution());

        $executions = $this->taskExecutionRepository->findAll();
        $this->assertCount(1, $executions);

        $this->assertEquals(TestHandler::class, $executions[0]->getHandlerClass());
        $this->assertGreaterThanOrEqual($date, $executions[0]->getScheduleTime());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommand()
    {
        return self::$kernel->getContainer()->get('task.command.schedule_task');
    }
}
