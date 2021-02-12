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

use Symfony\Component\Console\Output\OutputInterface;
use Task\TaskBundle\Tests\Functional\BaseCommandTestCase;
use Task\TaskBundle\Tests\Functional\TestHandler;

/**
 * Tests for RunHandlerCommand.
 */
class RunHandlerCommandTest extends BaseCommandTestCase
{
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('No workload.', $output);
    }

    public function testExecuteWithWorkload()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
                'workload' => 'Test workload 1',
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(strrev('Test workload 1'), $output);
    }

    public function testExecuteWithWorkloadNoVerbosity()
    {
        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'handlerClass' => TestHandler::class,
                'workload' => 'Test workload 1',
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertEquals('', $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommand()
    {
        return self::$kernel->getContainer()->get('task.command.run_handler');
    }
}
