<?php

namespace Task\TaskBundle\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Task\Storage\StorageInterface;
use Task\TaskBundle\Entity\Task as TaskEntity;
use Task\TaskBundle\Entity\TaskRepository;
use Task\TaskInterface;

class DoctrineStorage implements StorageInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function store(TaskInterface $task)
    {
        $entity = new TaskEntity();
        $this->setTask($entity, $task);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findScheduled()
    {
        return array_map(
            function (TaskEntity $entity) {
                return $entity->getTask();
            },
            $this->taskRepository->findScheduled()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return array_map(
            function (TaskEntity $entity) {
                return $entity->getTask();
            },
            $this->taskRepository->findAll()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(TaskInterface $task)
    {
        $entity = $this->taskRepository->findByUuid($task->getUuid());
        $this->setTask($entity, $task);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->taskRepository->deleteAll();
        $this->entityManager->flush();
    }

    private function setTask(TaskEntity $entity, TaskInterface $task)
    {
        $entity->setTask($task);
        $entity->setUuid($task->getUuid());
        $entity->setCompleted($task->isCompleted());
        $entity->setExecutionDate($task->getExecutionDate());
    }
}
