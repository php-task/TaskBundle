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

use Task\TaskInterface;

/**
 * Indicates not supported method.
 */
class NotSupportedMethodException extends \Exception
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @param string $property
     * @param TaskInterface $task
     */
    public function __construct($property, TaskInterface $task)
    {
        parent::__construct(
            sprintf('Property "%s" is not supported for task-class "%s".', $property, get_class($task))
        );

        $this->property = $property;
        $this->task = $task;
    }

    /**
     * Returns property.
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Returns task.
     *
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }
}
