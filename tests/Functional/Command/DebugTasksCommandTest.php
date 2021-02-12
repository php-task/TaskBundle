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

use Task\Execution\TaskExecutionInterface;
use Task\TaskBundle\Tests\Functional\BaseCommandTestCase;
use Task\TaskStatus;

/**
 * Tests for DebugTasksCommand.
 */
class DebugTasksCommandTest extends BaseCommandTestCase
{
    public function testExecute()
    {
        $task = $this->createTask('Test workload 1');

        /** @var TaskExecutionInterface[] $executions */
        $executions = [
            $this->createTaskExecution($task, new \DateTime('-1 hour'), TaskStatus::COMPLETED),
            $this->createTaskExecution($task, new \DateTime('-2 hour'), TaskStatus::COMPLETED),
        ];

        $executions[0]->setResult(strrev($executions[0]->getWorkload()));
        $executions[0]->setDuration(0.1);
        $executions[1]->setResult(strrev($executions[1]->getWorkload()));
        $executions[1]->setDuration(0.0001);

        if (self::$kernel->getContainer()->has('doctrine')) {
            $this->getEntityManager()->flush();
        }

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString($executions[0]->getUuid(), $output);
        $this->assertStringContainsString($executions[1]->getUuid(), $output);
        $this->assertStringContainsString('100000ms', $output);
        $this->assertStringContainsString('100ms', $output);
        $this->assertStringContainsString('completed', $output);
    }

    public function testExecutePaginated()
    {
        $task = $this->createTask('Test workload 1');

        /** @var TaskExecutionInterface[] $executions */
        $executions = [
            $this->createTaskExecution($task, new \DateTime('-1 hour')),
            $this->createTaskExecution($task, new \DateTime('-2 hour')),
            $this->createTaskExecution($task, new \DateTime('+1 hour')),
        ];

        if (self::$kernel->getContainer()->has('doctrine')) {
            $this->getEntityManager()->flush();
        }

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                '--page-size' => 2,
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString($executions[0]->getUuid(), $output);
        $this->assertStringContainsString($executions[1]->getUuid(), $output);
        $this->assertStringNotContainsString($executions[2]->getUuid(), $output);

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                '--page' => 2,
                '--page-size' => 2,
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringNotContainsString($executions[0]->getUuid(), $output);
        $this->assertStringNotContainsString($executions[1]->getUuid(), $output);
        $this->assertStringContainsString($executions[2]->getUuid(), $output);

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                '--page' => 3,
                '--page-size' => 2,
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringNotContainsString($executions[0]->getUuid(), $output);
        $this->assertStringNotContainsString($executions[1]->getUuid(), $output);
        $this->assertStringNotContainsString($executions[2]->getUuid(), $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommand()
    {
        return self::$kernel->getContainer()->get('task.command.debug_tasks');
    }
}
