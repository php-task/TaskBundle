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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Runner\TaskRunnerInterface;
use Task\Scheduler\TaskSchedulerInterface;

/**
 * Run pending tasks.
 */
class RunCommand extends Command
{
    /**
     * @var TaskRunnerInterface
     */
    private $runner;

    /**
     * @var TaskSchedulerInterface
     */
    private $scheduler;

    /**
     * @param string $name
     * @param TaskRunnerInterface $runner
     * @param TaskSchedulerInterface $scheduler
     */
    public function __construct($name, TaskRunnerInterface $runner, TaskSchedulerInterface $scheduler)
    {
        parent::__construct($name);

        $this->runner = $runner;
        $this->scheduler = $scheduler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Run pending tasks')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command runs the pending task-executions. During the execution the status
of the executions will be updated.

    $ %command.full_name%
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->runner->runTasks();
        $this->scheduler->scheduleTasks();

        return 0;
    }
}
