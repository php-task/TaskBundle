<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle;

use Task\TaskBundle\Entity\Task;
use Task\TaskBundle\Entity\TaskExecution;
use Task\TaskInterface;

/**
 * Factory which returns doctrine entities for tasks and task-execution.
 */
class Factory extends \Task\Factory
{
    /**
     * {@inheritdoc}
     */
    public function createTask($handlerClass, $workload)
    {
        return new Task($handlerClass, $workload);
    }

    /**
     * {@inheritdoc}
     */
    public function createTaskExecution(TaskInterface $task, \DateTime $scheduleTime)
    {
        return new TaskExecution($task, $task->getHandlerClass(), $scheduleTime, $task->getWorkload());
    }
}
