<?php

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\TaskRunner\TaskRunnerInterface;

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
    private $taskRunner;

    public function __construct(TaskRunnerInterface $taskRunner)
    {
        parent::__construct('task:run');

        $this->taskRunner = $taskRunner;
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
        $this->taskRunner->run();
    }
}
