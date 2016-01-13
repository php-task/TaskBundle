<?php

namespace Unit\Command;

use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Handler\RegistryInterface;
use Task\TaskBundle\Command\RunHandlerCommand;

class RunHandlerCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $scheduler = $this->prophesize(RegistryInterface::class);
        $command = new RunHandlerCommand($scheduler->reveal());

        $this->assertEquals('task:run:handler', $command->getName());
    }

    public function runProvider()
    {
        return [
            ['test-handler', 'test-workload'],
            ['test-handler-1', 'test-workload-1'],
        ];
    }

    /**
     * @dataProvider runProvider
     */
    public function testRun($handlerName, $workload)
    {
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->bind(Argument::any())->willReturn(true);
        $input->validate()->willReturn(true);
        $input->isInteractive()->willReturn(false);
        $input->hasArgument('command')->willReturn(false);

        $input->getArgument('handler')->willReturn($handlerName);
        $input->getArgument('workload')->willReturn($workload);

        $registry = $this->prophesize(RegistryInterface::class);
        $command = new RunHandlerCommand($registry->reveal());

        $command->run($input->reveal(), $output->reveal());

        $registry->run($handlerName, $workload)->shouldBeCalledTimes(1);
    }
}
