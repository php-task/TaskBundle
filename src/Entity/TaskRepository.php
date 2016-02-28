<?php

namespace Task\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function findEndBefore(\DateTime $dateTime)
    {
        $this->createQueryBuilder('t')
            ->where('t.lastExecution IS NOT NULL OR t.lastExecution > :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->getQuery()->getResult();
    }
}
