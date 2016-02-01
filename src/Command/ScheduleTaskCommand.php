<?php

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\SchedulerInterface;

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

    public function __construct($name, SchedulerInterface $scheduler)
    {
        parent::__construct($name);

        $this->scheduler = $scheduler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Run pending tasks')
            ->addArgument('handler', InputArgument::REQUIRED)
            ->addArgument('workload', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->scheduler
            ->createTask($input->getArgument('handler'), $input->getArgument('workload'))
            ->schedule();
    }
}
