<?php

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addArgument('workload', InputArgument::OPTIONAL)
            ->addOption('cron-expression', 'c', InputOption::VALUE_REQUIRED)
            ->addOption('end-date', 'e', InputOption::VALUE_REQUIRED)
            ->addOption('key', 'k', InputOption::VALUE_REQUIRED);
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
        $key = $input->getOption('key');

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

        if ($key !== null) {
            $taskBuilder->setKey($key);
        }

        $taskBuilder->schedule();
    }
}
