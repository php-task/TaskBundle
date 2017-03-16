<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Builder;

use Task\Builder\TaskBuilder as BaseTaskBuilder;
use Task\Builder\TaskBuilderInterface;
use Task\Scheduler\TaskSchedulerInterface;
use Task\TaskBundle\Entity\Task;
use Task\TaskInterface;

/**
 * Extends base task-builder.
 */
class TaskBuilder extends BaseTaskBuilder
{
    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @param TaskInterface $task
     * @param TaskSchedulerInterface $taskScheduler
     */
    public function __construct(TaskInterface $task, TaskSchedulerInterface $taskScheduler)
    {
        parent::__construct($task, $taskScheduler);

        $this->task = $task;
    }

    /**
     * Set system-key.
     *
     * @param string $systemKey
     *
     * @return TaskBuilderInterface
     *
     * @throws NotSupportedMethodException
     */
    public function setSystemKey($systemKey)
    {
        if (!$this->task instanceof Task) {
            throw new NotSupportedMethodException('systemKey', $this->task);
        }

        $this->task->setSystemKey($systemKey);

        return $this;
    }
}
