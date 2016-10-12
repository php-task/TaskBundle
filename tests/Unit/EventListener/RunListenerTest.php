<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Unit\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Task\Runner\TaskRunnerInterface;
use Task\TaskBundle\EventListener\RunListener;

/**
 * Tests for class RunListener.
 */
class RunListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $event = $this->prophesize(Event::class);
        $taskRunner = $this->prophesize(TaskRunnerInterface::class);

        $listener = new RunListener($taskRunner->reveal());

        $listener->run($event->reveal());

        $taskRunner->runTasks()->shouldBeCalledTimes(1);
    }
}
