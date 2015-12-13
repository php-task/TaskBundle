<?php

namespace Unit\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Task\SchedulerInterface;
use Task\TaskBundle\EventListener\RunListener;

class RunListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $event = $this->prophesize(Event::class);
        $scheduler = $this->prophesize(SchedulerInterface::class);

        $listener = new RunListener($scheduler->reveal());

        $listener->run($event->reveal());

        $scheduler->run()->shouldBeCalledTimes(1);
    }
}
