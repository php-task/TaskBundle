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

use Task\Lock\LockInterface;

/**
 * Implements LockInterface which does nothing.
 */
class NullLock implements LockInterface
{
    /**
     * {@inheritdoc}
     */
    public function acquire($task)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($task)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function release($task)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAcquired($task)
    {
        return false;
    }
}
