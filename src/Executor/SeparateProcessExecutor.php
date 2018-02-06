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

use Task\Execution\TaskExecutionInterface;
use Task\Executor\ExecutorInterface;
use Task\Executor\FailedException;
use Task\Executor\RetryTaskHandlerInterface;
use Task\Handler\TaskHandlerFactoryInterface;
use Task\Storage\TaskExecutionRepositoryInterface;

/**
 * Uses a separate process to start the executions via console-command.
 */
class SeparateProcessExecutor implements ExecutorInterface
{
    /**
     * @var TaskHandlerFactoryInterface
     */
    private $handlerFactory;

    /**
     * @var TaskExecutionRepositoryInterface
     */
    private $executionRepository;

    /**
     * @var ExecutionProcessFactory
     */
    private $processFactory;

    /**
     * @param TaskHandlerFactoryInterface $handlerFactory
     * @param TaskExecutionRepositoryInterface $executionRepository
     * @param ExecutionProcessFactory $processFactory
     */
    public function __construct(
        TaskHandlerFactoryInterface $handlerFactory,
        TaskExecutionRepositoryInterface $executionRepository,
        ExecutionProcessFactory $processFactory
    ) {
        $this->handlerFactory = $handlerFactory;
        $this->executionRepository = $executionRepository;
        $this->processFactory = $processFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(TaskExecutionInterface $execution)
    {
        $attempts = $this->getMaximumAttempts($execution->getHandlerClass());
        $lastException = null;

        for ($attempt = 0; $attempt < $attempts; ++$attempt) {
            try {
                return $this->handle($execution);
            } catch (FailedException $exception) {
                throw $exception;
            } catch (SeparateProcessException $exception) {
                if ($execution->getAttempts() < $attempts) {
                    $execution->incrementAttempts();
                    $this->executionRepository->save($execution);
                }

                $lastException = $exception;
            }
        }

        // maximum attempts to pass executions are reached
        throw new FailedException($lastException);
    }

    /**
     * Returns maximum attempts for specified handler.
     *
     * @param string $handlerClass
     *
     * @return int
     */
    private function getMaximumAttempts($handlerClass)
    {
        $handler = $this->handlerFactory->create($handlerClass);
        if (!$handler instanceof RetryTaskHandlerInterface) {
            return 1;
        }

        return $handler->getMaximumAttempts();
    }

    /**
     * Handle execution by using console-command.
     *
     * @param TaskExecutionInterface $execution
     *
     * @return string
     *
     * @throws FailedException
     * @throws SeparateProcessException
     */
    private function handle(TaskExecutionInterface $execution)
    {
        $process = $this->processFactory->create($execution->getUuid());
        $process->run();

        if (!$process->isSuccessful()) {
            throw $this->createException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * Create the correct exception.
     *
     * FailedException for failed executions.
     * SeparateProcessExceptions for any exception during execution.
     *
     * @param string $errorOutput
     *
     * @return FailedException|SeparateProcessException
     */
    private function createException($errorOutput)
    {
        if (0 !== strpos($errorOutput, FailedException::class)) {
            return new SeparateProcessException($errorOutput);
        }

        $errorOutput = trim(str_replace(FailedException::class, '', $errorOutput));

        return new FailedException(new SeparateProcessException($errorOutput));
    }
}
