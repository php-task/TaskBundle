<?php

namespace Task\TaskBundle\DoctrineStorage;

use Doctrine\Common\Persistence\ObjectManager;
use Task\Execution\TaskExecutionInterface;
use Task\Storage\TaskExecutionRepositoryInterface;
use Task\TaskBundle\Entity\TaskExecutionRepository as ORMTaskExecutionRepository;
use Task\TaskInterface;

class TaskExecutionRepository implements TaskExecutionRepositoryInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ORMTaskExecutionRepository
     */
    private $taskExecutionRepository;

    /**
     * {@inheritdoc}
     */
    public function save(TaskExecutionInterface $execution)
    {
        $this->objectManager->persist($execution);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function add(TaskExecutionInterface $execution)
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findScheduled()
    {
        return $this->taskExecutionRepository->findScheduled(new \DateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function findByStartTime(TaskInterface $task, \DateTime $scheduleTime)
    {
        return $this->taskExecutionRepository->findByStartTime($task, $scheduleTime);
    }
}
