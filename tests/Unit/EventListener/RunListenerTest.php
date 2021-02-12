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

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Contracts\EventDispatcher\Event;
use Task\Runner\TaskRunnerInterface;
use Task\TaskBundle\EventListener\RunListener;

/**
 * Tests for class RunListener.
 */
class RunListenerTest extends TestCase
{
    public function testRun()
    {
        if (\class_exists(LegacyEvent::class)) {
            $event = $this->prophesize(LegacyEvent::class);
        } else {
            $event = $this->prophesize(Event::class);
        }

        $taskRunner = $this->prophesize(TaskRunnerInterface::class);

        $listener = new RunListener($taskRunner->reveal());

        $listener->run($event->reveal());

        $taskRunner->runTasks()->shouldBeCalledTimes(1);
    }
}
