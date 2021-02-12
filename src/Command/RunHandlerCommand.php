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
use Symfony\Component\Console\Output\OutputInterface;
use Task\Handler\TaskHandlerFactoryInterface;

/**
 * Run pending tasks.
 */
class RunHandlerCommand extends Command
{
    /**
     * @var TaskHandlerFactoryInterface
     */
    private $handlerFactory;

    /**
     * @param string $name
     * @param TaskHandlerFactoryInterface $handlerFactory
     */
    public function __construct($name, TaskHandlerFactoryInterface $handlerFactory)
    {
        parent::__construct($name);

        $this->handlerFactory = $handlerFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Run handler')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command run given handler.

    $ %command.full_name% AppBundle\\Handler\\ImageHandler ./img/test-image.jpg
EOT
            )
        ->addArgument('handlerClass', InputArgument::REQUIRED, 'Handler which will be called')
        ->addArgument('workload', InputArgument::OPTIONAL, 'This will be passed to the handler');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handlerClass = $input->getArgument('handlerClass');
        $workload = $input->getArgument('workload');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf('Run command "%s" with workload "%s"', $handlerClass, $workload));
        }

        $result = $this->handlerFactory->create($handlerClass)->handle($workload);

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf('Result: %s', json_encode($result)));
        }

        return 0;
    }
}
