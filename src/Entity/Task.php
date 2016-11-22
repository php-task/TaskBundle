<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Entity;

use Cron\CronExpression;
use Task\Task as BaseTask;

/**
 * Extends base task with id and interval-expression which will be stored in database.
 */
class Task extends BaseTask
{
    /**
     * @var string
     */
    private $intervalExpression;

    /**
     * @return mixed
     */
    public function getIntervalExpression()
    {
        return $this->intervalExpression;
    }

    public function getInterval()
    {
        if (null === $this->interval && null !== $this->intervalExpression) {
            $this->interval = CronExpression::factory($this->intervalExpression);
        }

        return parent::getInterval();
    }

    /**
     * {@inheritdoc}
     */
    public function setInterval(CronExpression $interval, \DateTime $firstExecution = null, \DateTime $lastExecution = null)
    {
        parent::setInterval($interval, $firstExecution, $lastExecution);

        $this->intervalExpression = $interval->getExpression();
    }
}
