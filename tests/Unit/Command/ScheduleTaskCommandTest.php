<?php

namespace Unit\Command;

use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\SchedulerInterface;
use Task\TaskBuilderInterface;
use Task\TaskBundle\Command\ScheduleTaskCommand;

class ScheduleTaskCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $scheduler = $this->prophesize(SchedulerInterface::class);
        $command = new ScheduleTaskCommand('task:schedule:task', $scheduler->reveal());

        $this->assertEquals('task:schedule:task', $command->getName());
        $this->assertTrue($command->getDefinition()->hasArgument('handler'));
        $this->assertTrue($command->getDefinition()->hasArgument('workload'));
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
    public function testRun($handler, $workload)
    {
        $taskBuilder = $this->prophesize(TaskBuilderInterface::class);

        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $input->bind(Argument::any())->willReturn(true);
        $input->validate()->willReturn(true);
        $input->isInteractive()->willReturn(false);
        $input->hasArgument('command')->willReturn(false);

        $input->getArgument('handler')->willReturn($handler);
        $input->getArgument('workload')->willReturn($workload);

        $scheduler = $this->prophesize(SchedulerInterface::class);
        $command = new ScheduleTaskCommand('task:schedule:task', $scheduler->reveal());

        $scheduler->createTask($handler, $workload)->shouldBeCalledTimes(1)->willReturn($taskBuilder->reveal());

        $command->run($input->reveal(), $output->reveal());

        $taskBuilder->schedule()->shouldBeCalledTimes(1);
    }
}
