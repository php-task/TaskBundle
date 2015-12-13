<?php

namespace Unit\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\SchedulerInterface;
use Task\TaskBundle\Command\RunCommand;

class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $scheduler = $this->prophesize(SchedulerInterface::class);
        $command = new RunCommand($scheduler->reveal());

        $this->assertEquals('task:run', $command->getName());
    }

    public function testRun()
    {
        $input = $this->prophesize(InputInterface::class);
        $output = $this->prophesize(OutputInterface::class);

        $scheduler = $this->prophesize(SchedulerInterface::class);
        $command = new RunCommand($scheduler->reveal());

        $command->run($input->reveal(), $output->reveal());

        $scheduler->run()->shouldBeCalledTimes(1);
    }
}
