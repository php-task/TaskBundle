<?php

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Runner\TaskRunnerInterface;

/**
 * Run pending tasks.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class RunCommand extends Command
{
    /**
     * @var TaskRunnerInterface
     */
    private $runner;

    public function __construct($name, TaskRunnerInterface $scheduler)
    {
        parent::__construct($name);

        $this->runner = $scheduler;
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
    }
}
