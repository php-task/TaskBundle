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

use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param string $name
     * @param TaskSchedulerInterface $runner
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($name, TaskSchedulerInterface $runner, EntityManagerInterface $entityManager = null)
    {
        parent::__construct($name);

        $this->scheduler = $runner;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Run pending tasks')
            ->addArgument('handlerClass', InputArgument::REQUIRED)
            ->addArgument('workload', InputArgument::OPTIONAL)
            ->addOption('cron-expression', 'c', InputOption::VALUE_REQUIRED)
            ->addOption('execution-date', null, InputOption::VALUE_REQUIRED)
            ->addOption('end-date', null, InputOption::VALUE_REQUIRED);
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

        if ($this->entityManager) {
            $this->entityManager->flush();
        }
    }
}
