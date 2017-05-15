<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Locking;

use Task\Execution\TaskExecutionInterface;
use Task\Lock\LockInterface;

/**
 * Implements LockInterface which does nothing.
 */
class NullLock implements LockInterface
{
    /**
     * {@inheritdoc}
     */
    public function acquire(TaskExecutionInterface $execution)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(TaskExecutionInterface $execution)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function release(TaskExecutionInterface $execution)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAcquired(TaskExecutionInterface $execution)
    {
        return false;
    }
}
