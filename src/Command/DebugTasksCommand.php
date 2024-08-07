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
    protected function configure(): void
    {
        $this->setDescription('Debug tasks')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command dumps the information about tasks.

    $ %command.full_name% -p 1 --page-size 10

Pagination is possible with the optional options 'p' and 'page-size'.
EOT
            )
            ->addOption('page', 'p', InputOption::VALUE_REQUIRED, 'Specifies page for pagination', 1)
            ->addOption('page-size', null, InputOption::VALUE_REQUIRED, 'Specifies page-size for pagination', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
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

        return 0;
    }
}
