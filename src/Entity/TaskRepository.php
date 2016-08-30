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

use Doctrine\ORM\EntityRepository;
use Task\TaskInterface;

/**
 * Repository for task.
 */
class TaskRepository extends EntityRepository
{
    /**
     * Returns task where last-execution is before given date-time.
     *
     * @param \DateTime $dateTime
     *
     * @return TaskInterface[]
     */
    public function findEndBefore(\DateTime $dateTime)
    {
        return $this->createQueryBuilder('t')
            ->where('t.lastExecution IS NULL OR t.lastExecution >= :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult();
    }
}
