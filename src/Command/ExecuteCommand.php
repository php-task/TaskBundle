<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Task\Event\Events;
use Task\Event\TaskEvent;
use Task\Executor\FailedException;
use Task\Handler\TaskHandlerFactoryInterface;
use Task\Storage\TaskExecutionRepositoryInterface;

/**
 * Executes given execution identified by uuid.
 */
class ExecuteCommand extends Command
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param string $name
     * @param TaskHandlerFactoryInterface $handlerFactory
     * @param TaskExecutionRepositoryInterface $executionRepository
     */
    public function __construct(
        $name,
        TaskHandlerFactoryInterface $handlerFactory,
        TaskExecutionRepositoryInterface $executionRepository,
        EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($name);

        $this->handlerFactory = $handlerFactory;
        $this->executionRepository = $executionRepository;
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('uuid', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        $execution = $this->executionRepository->findByUuid($input->getArgument('uuid'));
        $handler = $this->handlerFactory->create($execution->getHandlerClass());

        try {
            $this->eventDispatcher->dispatch(new TaskEvent($execution->getTask()),Events::TASK_BEFORE);
            $result = $handler->handle($execution->getWorkload());
            $this->eventDispatcher->dispatch(new TaskEvent($execution),Events::TASK_AFTER);
        } catch (\Exception $exception) {
            if ($exception instanceof FailedException) {
                $errorOutput->writeln(FailedException::class);
                $exception = $exception->getPrevious();
            }

            $errorOutput->writeln($exception->__toString());

            // Process exit-code: 0 = OK, >1 = FAIL
            return 1;
        }

        $output->write($result);

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
