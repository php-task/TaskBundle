<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Executor;

use Symfony\Component\Process\ProcessBuilder;
use Task\Execution\TaskExecutionInterface;
use Task\Runner\ExecutorInterface;
use Task\TaskBundle\Command\ExecuteCommand;

/**
 * Uses a process to start the executions via console-command.
 */
class ProcessExecutor implements ExecutorInterface
{
    /**
     * @var string
     */
    private $consoleFile;

    /**
     * @param string $consoleFile
     */
    public function __construct($consoleFile)
    {
        $this->consoleFile = $consoleFile;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(TaskExecutionInterface $execution)
    {
        $process = ProcessBuilder::create([$this->consoleFile, 'task:execute', $execution->getUuid()])->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessException($process->getErrorOutput());
        }

        return $this->extractResult($process->getOutput());
    }

    /**
     * Extract the result from output.
     *
     * @param string $output
     *
     * @return string
     */
    private function extractResult($output)
    {
        $match = preg_match(
            sprintf(
                '/%s(?<result>.*)%s/s',
                preg_quote(ExecuteCommand::START_RESULT),
                preg_quote(ExecuteCommand::END_RESULT)
            ),
            $output,
            $matches
        );

        if (!$match) {
            return;
        }

        return $matches['result'];
    }
}
