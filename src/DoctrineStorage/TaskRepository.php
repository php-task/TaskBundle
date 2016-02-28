<?php

namespace Task\TaskBundle\DoctrineStorage;

use Doctrine\Common\Persistence\ObjectManager;
use Task\Storage\TaskRepositoryInterface;
use Task\TaskBundle\Entity\TaskRepository as ORMTaskRepository;
use Task\TaskInterface;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ORMTaskRepository
     */
    private $taskRepository;

    /**
     * {@inheritdoc}
     */
    public function add(TaskInterface $task)
    {
        $this->objectManager->persist($task);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->taskRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findEndBeforeNow()
    {
        return $this->taskRepository->findEndBefore(new \DateTime());
    }
}
