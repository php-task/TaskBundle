<?php

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Storage\StorageInterface;

/**
 * Run pending tasks.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class DebugTasksCommand extends Command
{
    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct($name, StorageInterface $storage)
    {
        parent::__construct($name);

        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Debug tasks')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, '', null)
            ->addOption('key', 'k', InputOption::VALUE_REQUIRED, '', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getOption('key');
        $limit = $input->getOption('limit');

        if (null !== $key) {
            $tasks = $this->storage->findByKey($key, $limit);
        } else {
            $tasks = $this->storage->findAll($limit);
        }

        $table = new Table($output);
        $table->setHeaders(['uuid', 'key', 'task-name', 'execution-date', 'completed', 'start', 'duration']);

        foreach ($tasks as $task) {
            $start = null;
            $duration = null;
            if ($task->getLastExecution()) {
                $start = $task->getLastExecution()->getFinishedAtAsDateTime()->format(\DateTime::RFC3339);
                $duration = $task->getLastExecution()->getExecutionDuration();
            }

            $table->addRow(
                [
                    $task->getUuid(),
                    $task->getKey(),
                    $task->getTaskName(),
                    $task->getExecutionDate()->format(\DateTime::RFC3339),
                    $task->isCompleted(),
                    $start,
                    $duration,
                ]
            );
        }

        $table->render();
    }
}
