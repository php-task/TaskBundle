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

/**
 * Uses a separate process to start the executions via console-command.
 */
class SeparateProcessExecutor implements ExecutorInterface
{
    /**
     * @var string
     */
    private $consolePath;

    /**
     * @var string
     */
    private $environment;

    /**
     * @param string $consolePath
     * @param string $environment
     */
    public function __construct($consolePath, $environment)
    {
        $this->consolePath = $consolePath;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(TaskExecutionInterface $execution)
    {
        $process = ProcessBuilder::create(
            [$this->consolePath, 'task:execute', $execution->getUuid(), '-e ' . $this->environment]
        )->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new SeparateProcessException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
