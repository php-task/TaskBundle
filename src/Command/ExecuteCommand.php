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
use Task\Handler\TaskHandlerFactoryInterface;
use Task\Storage\TaskExecutionRepositoryInterface;

/**
 * Executes given execution identified by uuid.
 */
class ExecuteCommand extends Command
{
    const START_RESULT = '<?result';
    const END_RESULT = '?>';

    /**
     * @var TaskHandlerFactoryInterface
     */
    private $handlerFactory;

    /**
     * @var TaskExecutionRepositoryInterface
     */
    private $executionRepository;

    /**
     * @param string $name
     * @param TaskHandlerFactoryInterface $handlerFactory
     * @param TaskExecutionRepositoryInterface $executionRepository
     */
    public function __construct(
        $name,
        TaskHandlerFactoryInterface $handlerFactory,
        TaskExecutionRepositoryInterface $executionRepository
    ) {
        parent::__construct($name);

        $this->handlerFactory = $handlerFactory;
        $this->executionRepository = $executionRepository;
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
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        $execution = $this->executionRepository->findByUuid($input->getArgument('uuid'));
        $handler = $this->handlerFactory->create($execution->getHandlerClass());

        try {
            $result = $handler->handle($execution->getWorkload());
        } catch (\Exception $e) {
            $errOutput->writeln($e->__toString());

            return 1;
        }

        $output->write(self::START_RESULT);
        $output->write($result);
        $output->write(self::END_RESULT);
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
