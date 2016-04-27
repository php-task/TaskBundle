<?php

namespace Task\TaskBundle;

use Task\TaskBundle\Entity\Task;
use Task\TaskBundle\Entity\TaskExecution;
use Task\TaskInterface;

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
