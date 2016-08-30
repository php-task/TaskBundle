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
use Doctrine\ORM\NoResultException;
use Task\Execution\TaskExecutionInterface;
use Task\TaskInterface;
use Task\TaskStatus;

/**
 * Repository for task-execution.
 */
class TaskExecutionRepository extends EntityRepository
{
    /**
     * Returns task-execution by task and scheduled-time.
     *
     * @param TaskInterface $task
     * @param \DateTime $scheduleTime
     *
     * @return TaskExecutionInterface
     */
    public function findByScheduledTime(TaskInterface $task, \DateTime $scheduleTime)
    {
        try {
            return $this->createQueryBuilder('e')
                ->innerJoin('e.task', 't')
                ->where('t.uuid = :uuid')
                ->andWhere('e.scheduleTime = :scheduleTime')
                ->setParameter('uuid', $task->getUuid())
                ->setParameter('scheduleTime', $scheduleTime)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return;
        }
    }

    /**
     * Returns scheduled task-execution.
     *
     * @param \DateTime|null $dateTime
     *
     * @return TaskExecutionInterface[]
     */
    public function findScheduled(\DateTime $dateTime = null)
    {
        if ($dateTime === null) {
            $dateTime = new \DateTime();
        }

        return $this->createQueryBuilder('e')
            ->innerJoin('e.task', 't')
            ->where('e.status = :status AND e.scheduleTime < :dateTime')
            ->setParameter('status', TaskStatus::PLANNED)
            ->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult();
    }
}
