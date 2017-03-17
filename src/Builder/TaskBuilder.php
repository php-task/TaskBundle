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
use Task\TaskBundle\Entity\Task;

/**
 * Extends base task-builder.
 */
class TaskBuilder extends BaseTaskBuilder
{
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
