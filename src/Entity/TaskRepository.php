<?php

namespace Task\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function findScheduled()
    {
        $query = $this->createQueryBuilder('task')
            ->where('completed = FALSE')
            ->andWhere('executionDate < :date')
            ->setParameter('date', new \DateTime())
            ->getQuery();

        return $query->getResult();
    }

    public function findByUuid($uuid)
    {
        return $this->findBy(['uuid' => $uuid]);
    }
}
