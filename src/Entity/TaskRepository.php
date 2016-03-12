<?php

namespace Task\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Task\Storage\TaskRepositoryInterface;
use Task\TaskInterface;

class TaskRepository extends EntityRepository implements TaskRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findEndBefore(\DateTime $dateTime)
    {
        return $this->createQueryBuilder('t')
            ->where('t.lastExecution IS NOT NULL OR t.lastExecution > :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function add(TaskInterface $task)
    {
        $this->_em->persist($task);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findEndBeforeNow()
    {
        return $this->findEndBefore(new \DateTime());
    }
}
