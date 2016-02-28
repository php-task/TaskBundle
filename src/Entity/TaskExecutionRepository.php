<?php

namespace Task\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Task\TaskInterface;
use Task\TaskStatus;

class TaskExecutionRepository extends EntityRepository
{
    public function findByStartTime(TaskInterface $task, \DateTime $scheduleTime)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.task', 't')
            ->where('t.uuid = :uuid')
            ->andWhere('e.scheduleTime = :scheduleTime')
            ->setParameter('uuid', $task->getUuid())
            ->setParameter('scheduleTime', $scheduleTime)
            ->getQuery()
            ->getResult();
    }

    public function findScheduled(\DateTime $dateTime)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.task', 't')
            ->where('e.status = :status AND e.scheduleTime < :dateTime')
            ->setParameter('status', TaskStatus::PLANNED)
            ->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult();
    }
}
