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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Storage\TaskExecutionRepositoryInterface;

/**
 * Run pending tasks.
 */
class DebugTasksCommand extends Command
{
    /**
     * @var TaskExecutionRepositoryInterface
     */
    private $taskExecutionRepository;

    /**
     * @param string $name
     * @param TaskExecutionRepositoryInterface $taskExecutionRepository
     */
    public function __construct($name, TaskExecutionRepositoryInterface $taskExecutionRepository)
    {
        parent::__construct($name);

        $this->taskExecutionRepository = $taskExecutionRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Debug tasks')
            ->addOption('page', 'p', InputOption::VALUE_REQUIRED, '', 1)
            ->addOption('page-size', null, InputOption::VALUE_REQUIRED, '', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $page = $input->getOption('page');
        $pageSize = $input->getOption('page-size');

        $executions = $this->taskExecutionRepository->findAll($page, $pageSize);

        $table = new Table($output);
        $table->setHeaders(['uuid', 'status', 'handler', 'schedule time', 'end time', 'duration']);

        foreach ($executions as $execution) {
            $table->addRow(
                [
                    $execution->getUuid(),
                    $execution->getStatus(),
                    $execution->getHandlerClass(),
                    $execution->getScheduleTime()->format(\DateTime::RFC3339),
                    !$execution->getEndTime() ? '' : $execution->getEndTime()->format(\DateTime::RFC3339),
                    (round($execution->getDuration(), 6) * 1000000) . 'ms',
                ]
            );
        }

        $table->render();
    }
}
