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
use Task\Scheduler\SchedulerInterface;

/**
 * Schedule task.
 */
class ScheduleTaskCommand extends Command
{
    /**
     * @var SchedulerInterface
     */
    private $scheduler;

    /**
     * @param string $name
     * @param SchedulerInterface $runner
     */
    public function __construct($name, SchedulerInterface $runner)
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
            ->setDescription('Run pending tasks')
            ->addArgument('handler', InputArgument::REQUIRED)
            ->addArgument('workload', InputArgument::OPTIONAL)
            ->addOption('cron-expression', 'c', InputOption::VALUE_REQUIRED)
            ->addOption('end-date', null, InputOption::VALUE_REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $input->getArgument('handler');
        $workload = $input->getArgument('workload');
        $cronExpression = $input->getOption('cron-expression');
        $endDateString = $input->getOption('end-date');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf('Schedule task "%s" with workload "%s"', $handler, $workload));
        }

        $taskBuilder = $this->scheduler->createTask($input->getArgument('handler'), $input->getArgument('workload'));

        if ($cronExpression !== null) {
            $endDate = null;
            if ($endDateString !== null) {
                $endDate = new \DateTime($endDateString);
            }

            $taskBuilder->cron($cronExpression, new \DateTime(), $endDate);
        }

        $this->scheduler->addTask($taskBuilder->getTask());
    }
}
