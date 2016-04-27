<?php

namespace Task\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Task\Execution\TaskExecutionInterface;
use Task\TaskInterface;
use Task\TaskStatus;

class TaskExecutionRepository extends EntityRepository
{
    public function findByStartTime(TaskInterface $task, \DateTime $scheduleTime)
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
            return null;
        }
    }

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

    public function save(TaskExecutionInterface $execution)
    {
        $this->_em->flush();
    }

    public function add(TaskExecutionInterface $execution)
    {
        $this->_em->persist($execution);
        $this->_em->flush();
    }
}
