<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Storage\ArrayStorage;

use Task\Storage\ArrayStorage\ArrayTaskExecutionRepository as BaseArrayTaskExecutionRepository;
use Task\TaskInterface;

/**
 * Accepts \DateTimeInterface and normalizes to \DateTimeImmutable so that
 * non-immutable instances returned by upstream cron scheduling code are
 * handled transparently.
 */
class ArrayTaskExecutionRepository extends BaseArrayTaskExecutionRepository
{
    public function create(TaskInterface $task, \DateTimeInterface $scheduleTime)
    {
        if (!$scheduleTime instanceof \DateTimeImmutable) {
            $scheduleTime = \DateTimeImmutable::createFromInterface($scheduleTime);
        }

        $execution = parent::create($task, $scheduleTime);
        // Initialize result to a serialized null so getResult() returns null
        // instead of false (unserialize(null) yields false in PHP 8).
        $execution->setResult(null);

        return $execution;
    }
}
