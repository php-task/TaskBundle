<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\EventListener;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Contracts\EventDispatcher\Event;
use Task\Runner\TaskRunnerInterface;

/**
 * Listens to event and run scheduled tasks.
 */
class RunListener
{
    /**
     * @var TaskRunnerInterface
     */
    private $taskRunner;

    /**
     * @param TaskRunnerInterface $taskRunner
     */
    public function __construct(TaskRunnerInterface $taskRunner)
    {
        $this->taskRunner = $taskRunner;
    }

    /**
     * Run scheduled tasks.
     *
     * @param Event|LegacyEvent $event
     */
    public function run($event)
    {
        $this->taskRunner->runTasks();
    }
}
