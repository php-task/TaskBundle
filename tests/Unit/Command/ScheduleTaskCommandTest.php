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
            ['test-handler'],
            ['test-handler', 'test-workload'],
            ['test-handler-1', 'test-workload-1'],
            ['test-handler', 'test-workload', '1 * * * *'],
            ['test-handler', 'test-workload', '1 * * * *', '+1 week'],
            ['test-handler', 'test-workload', '1 * * * *', '+1 week', 'test-key'],
            ['test-handler', 'test-workload', '1 * * * *', null, 'test-key'],
            ['test-handler', 'test-workload', null, null, 'test-key'],
            ['test-handler', 'test-workload', null, '+1 week', 'test-key'],
        ];
    }

    /**
     * @dataProvider runProvider
     */
    public function testRun($handler, $workload = null, $cronExpression = null, $endDateString = null, $key = null)
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
        $input->getOption('cron-expression')->willReturn($cronExpression);
        $input->getOption('end-date')->willReturn($endDateString);
        $input->getOption('key')->willReturn($key);

        $scheduler = $this->prophesize(SchedulerInterface::class);
        $command = new ScheduleTaskCommand('task:schedule:task', $scheduler->reveal());

        $scheduler->createTask($handler, $workload)->shouldBeCalledTimes(1)->willReturn($taskBuilder->reveal());

        if ($key !== null) {
            $taskBuilder->setKey($key)->shouldBeCalled();
        } else {
            $taskBuilder->setKey(Argument::any())->shouldNotBeCalled();
        }
        if ($cronExpression !== null) {
            $endDate = null;
            if ($endDateString !== null) {
                $endDate = new \DateTime($endDateString);
            }

            $taskBuilder->cron(
                $cronExpression,
                Argument::type(\DateTime::class),
                Argument::that(
                    function ($argument) use ($endDate) {
                        $this->assertEquals($endDate, $argument, '', 2);

                        return true;
                    }
                )
            )->shouldBeCalled()
                ->willReturn($taskBuilder->reveal());
        } else {
            $taskBuilder->cron(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled()
                ->willReturn($taskBuilder->reveal());
        }
        $taskBuilder->schedule()->shouldBeCalledTimes(1);

        $command->run($input->reveal(), $output->reveal());
    }
}
