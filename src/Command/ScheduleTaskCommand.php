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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Scheduler\TaskSchedulerInterface;

/**
 * Schedule task.
 */
class ScheduleTaskCommand extends Command
{
    /**
     * @var TaskSchedulerInterface
     */
    private $scheduler;

    /**
     * @param string $name
     * @param TaskSchedulerInterface $runner
     */
    public function __construct($name, TaskSchedulerInterface $runner)
    {
        parent::__construct($name);

        $this->scheduler = $runner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Schedule task')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command schedules given handler.

    $ %command.full_name% AppBundle\\Handler\\ImageHandler

Execute without any arguments in order to see schedule a single task, use the
<comment>--workload</comment> option in order to specify a workload or
<comment>--cron-expression</comment> to create a recurring task.
EOT
            )
            ->addArgument('handlerClass', InputArgument::REQUIRED, 'Handler which will be called')
            ->addArgument('workload', InputArgument::OPTIONAL, 'This will be passed to the handler')
            ->addOption('cron-expression', 'c', InputOption::VALUE_REQUIRED, 'Specifies interval for recurring task')
            ->addOption('execution-date', null, InputOption::VALUE_REQUIRED, 'Specifies execution-date for single task')
            ->addOption('end-date', null, InputOption::VALUE_REQUIRED, 'Specifies last run date for recurring tasks');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handlerClass = $input->getArgument('handlerClass');
        $workload = $input->getArgument('workload');
        $cronExpression = $input->getOption('cron-expression');
        $executionDateString = $input->getOption('execution-date');
        $endDateString = $input->getOption('end-date');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf('Schedule task "%s" with workload "%s"', $handlerClass, $workload));
        }

        $taskBuilder = $this->scheduler->createTask($handlerClass, $workload);

        if ($cronExpression !== null) {
            $endDate = null;
            if ($endDateString !== null) {
                $endDate = new \DateTime($endDateString);
            }

            $taskBuilder->cron($cronExpression, new \DateTime(), $endDate);
        }

        if ($executionDateString !== null) {
            $taskBuilder->executeAt(new \DateTime($executionDateString));
        }

        $taskBuilder->schedule();
    }
}
