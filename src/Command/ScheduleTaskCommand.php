<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Scheduler\SchedulerInterface;
use Task\Scheduler\Task;

/**
 * Schedule task.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class ScheduleTaskCommand extends Command
{
    /**
     * @var SchedulerInterface
     */
    private $scheduler;

    public function __construct(SchedulerInterface $scheduler)
    {
        parent::__construct('task:schedule:task');

        $this->scheduler = $scheduler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Run pending tasks')
            ->addArgument('workerName', InputArgument::REQUIRED)
            ->addArgument('workload', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->scheduler->schedule(new Task($input->getArgument('workerName'), $input->getArgument('workload')));
    }
}
