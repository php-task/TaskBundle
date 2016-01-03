<?php

namespace Task\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function findScheduled()
    {
        $query = $this->createQueryBuilder('task')
            ->where('task.completed = :completed')
            ->andWhere('task.executionDate <= :date')
            ->setParameter('completed', false)
            ->setParameter('date', new \DateTime())
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $uuid
     *
     * @return Task
     */
    public function findByUuid($uuid)
    {
        $query = $this->createQueryBuilder('task')
            ->where('task.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery();

        return $query->getSingleResult();
    }

    public function deleteAll()
    {
        $query = $this->_em->createQueryBuilder()
            ->delete($this->_entityName, 'task')
            ->getQuery();

        $query->execute();
    }
}
