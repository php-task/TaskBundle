<?php

namespace Task\TaskBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Handler\RegistryInterface;
use Task\SchedulerInterface;

/**
 * Run pending tasks.
 *
 * @author Alexander Schranz <alexander.schranz@massiveart.com>
 */
class RunHandlerCommand extends Command
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct('task:run:handler');

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Run pending tasks')
            ->addArgument('handler', InputArgument::REQUIRED)
            ->addArgument('workload', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $input->getArgument('handler');
        $workload = $input->getArgument('workload');

        $output->writeln(sprintf('Run command "%s" with workload "%s"', $handler, $workload));

        $result = $this->registry->run($handler, $workload);

        $output->writeln(sprintf('Result: "%s"', $result));
    }
}
