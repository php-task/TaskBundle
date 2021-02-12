<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Unit\Executor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Task\Execution\TaskExecutionInterface;
use Task\Executor\FailedException;
use Task\Executor\RetryTaskHandlerInterface;
use Task\Handler\TaskHandlerFactoryInterface;
use Task\Handler\TaskHandlerInterface;
use Task\Storage\TaskExecutionRepositoryInterface;
use Task\TaskBundle\Executor\ExecutionProcessFactory;
use Task\TaskBundle\Executor\SeparateProcessException;
use Task\TaskBundle\Executor\SeparateProcessExecutor;

class SeparateProcessExecutorTest extends TestCase
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
     * @var SeparateProcessExecutor
     */
    private $executor;

    /**
     * @var TaskHandlerInterface
     */
    private $handler;

    /**
     * @var RetryTaskHandlerInterface
     */
    private $retryHandler;

    /**
     * @var TaskExecutionInterface
     */
    private $execution;

    /**
     * @var Process
     */
    private $process;

    protected function setUp()
    {
        $this->handlerFactory = $this->prophesize(TaskHandlerFactoryInterface::class);
        $this->executionRepository = $this->prophesize(TaskExecutionRepositoryInterface::class);
        $this->processFactory = $this->prophesize(ExecutionProcessFactory::class);

        $this->executor = new SeparateProcessExecutor(
            $this->handlerFactory->reveal(), $this->executionRepository->reveal(), $this->processFactory->reveal()
        );

        $this->handler = $this->prophesize(TaskHandlerInterface::class);
        $this->retryHandler = $this->prophesize(TaskHandlerInterface::class);
        $this->retryHandler->willImplement(RetryTaskHandlerInterface::class);

        $this->handlerFactory->create('TaskHandler')->willReturn($this->handler->reveal());
        $this->handlerFactory->create('RetryTaskHandler')->willReturn($this->retryHandler->reveal());

        $this->execution = $this->prophesize(TaskExecutionInterface::class);
        $this->execution->getUuid()->willReturn('123-123-123');
        $this->execution->getAttempts()->willReturn(1);

        $this->process = $this->prophesize(Process::class);
        $this->processFactory->create('123-123-123')->willReturn($this->process->reveal());
    }

    public function testExecute()
    {
        $this->execution->getHandlerClass()->willReturn('TaskHandler');

        $this->process->run()->shouldBeCalled();
        $this->process->isSuccessful()->willReturn(true);

        $this->process->getOutput()->willReturn('TEST');

        $result = $this->executor->execute($this->execution->reveal());
        $this->assertEquals('TEST', $result);
    }

    public function testExecuteException()
    {
        $this->execution->getHandlerClass()->willReturn('TaskHandler');

        $this->process->run()->shouldBeCalled();
        $this->process->isSuccessful()->willReturn(false);

        $this->process->getErrorOutput()->willReturn('TEST');

        try {
            $this->executor->execute($this->execution->reveal());

            $this->fail('No FailedException was thrown');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(FailedException::class, $exception);
            $this->assertInstanceOf(SeparateProcessException::class, $exception->getPrevious());
            $this->assertEquals('TEST', $exception->getPrevious()->__toString());
        }
    }

    public function testExecuteFailedException()
    {
        $this->execution->getHandlerClass()->willReturn('TaskHandler');

        $this->process->run()->shouldBeCalled();
        $this->process->isSuccessful()->willReturn(false);

        $this->process->getErrorOutput()->willReturn(FailedException::class . PHP_EOL . 'TEST');

        try {
            $this->executor->execute($this->execution->reveal());

            $this->fail('No FailedException was thrown');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(FailedException::class, $exception);
            $this->assertInstanceOf(SeparateProcessException::class, $exception->getPrevious());
            $this->assertEquals('TEST', $exception->getPrevious()->__toString());
        }
    }

    public function testExecuteRetryFailedException()
    {
        $this->execution->getHandlerClass()->willReturn('RetryTaskHandler');
        $this->retryHandler->getMaximumAttempts()->willReturn(3);

        $this->execution->incrementAttempts()->shouldNotBeCalled();

        $this->process->run()->shouldBeCalledTimes(1);
        $this->process->isSuccessful()->willReturn(false);

        $this->process->getErrorOutput()->willReturn(FailedException::class . PHP_EOL . 'TEST');

        try {
            $this->executor->execute($this->execution->reveal());

            $this->fail('No FailedException was thrown');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(FailedException::class, $exception);
            $this->assertInstanceOf(SeparateProcessException::class, $exception->getPrevious());
            $this->assertEquals('TEST', $exception->getPrevious()->__toString());
        }
    }

    public function testExecuteRetryException()
    {
        $this->execution->getHandlerClass()->willReturn('RetryTaskHandler');
        $this->retryHandler->getMaximumAttempts()->willReturn(3);

        $attempts = 1;
        $this->execution->incrementAttempts()->will(
            function () use (&$attempts) {
                ++$attempts;

                return $this;
            }
        );
        $this->execution->getAttempts()->will(
            function () use (&$attempts) {
                return $attempts;
            }
        );

        $this->executionRepository->save($this->execution->reveal())->shouldBeCalled(2);

        $this->process->run()->shouldBeCalledTimes(3);
        $this->process->isSuccessful()->willReturn(false);

        $this->process->getErrorOutput()->willReturn('TEST');

        try {
            $this->executor->execute($this->execution->reveal());

            $this->fail('No FailedException was thrown');
        } catch (\Exception $exception) {
            $this->assertInstanceOf(FailedException::class, $exception);
            $this->assertInstanceOf(SeparateProcessException::class, $exception->getPrevious());
            $this->assertEquals('TEST', $exception->getPrevious()->__toString());
            $this->assertEquals(3, $attempts);
        }
    }
}
