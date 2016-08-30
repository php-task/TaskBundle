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
use Task\Scheduler\SchedulerInterface;

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
     * @var SchedulerInterface
     */
    private $scheduler;

    /**
     * @param string $name
     * @param TaskRunnerInterface $runner
     * @param SchedulerInterface $scheduler
     */
    public function __construct($name, TaskRunnerInterface $runner, SchedulerInterface $scheduler)
    {
        parent::__construct($name);

        $this->runner = $runner;
        $this->scheduler = $scheduler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Run pending tasks');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runner->runTasks();
        $this->scheduler->scheduleTasks();
    }
}
