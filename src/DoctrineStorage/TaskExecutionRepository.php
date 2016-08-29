<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\DoctrineStorage;

use Doctrine\Common\Persistence\ObjectManager;
use Task\Execution\TaskExecutionInterface;
use Task\Execution\TaskExecutionRepositoryInterface;
use Task\TaskBundle\Entity\TaskExecutionRepository as ORMTaskExecutionRepository;
use Task\TaskInterface;

/**
 * Task-execution storage using doctrine.
 */
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
     * @param ObjectManager $objectManager
     * @param ORMTaskExecutionRepository $taskExecutionRepository
     */
    public function __construct(ObjectManager $objectManager, ORMTaskExecutionRepository $taskExecutionRepository)
    {
        $this->objectManager = $objectManager;
        $this->taskExecutionRepository = $taskExecutionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function store(TaskExecutionInterface $execution)
    {
        $this->objectManager->persist($execution);

        // FIXME move this flush to somewhere else (:
        $this->objectManager->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TaskExecutionInterface $execution)
    {
        $this->objectManager->persist($execution);

        // FIXME move this flush to somewhere else (:
        $this->objectManager->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function findByStartTime(TaskInterface $task, \DateTime $scheduleTime)
    {
        return $this->taskExecutionRepository->findByScheduledTime($task, $scheduleTime);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($limit = null)
    {
        return $this->taskExecutionRepository->findBy([], ['scheduleTime' => 'ASC'], $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function findScheduled()
    {
        return $this->taskExecutionRepository->findScheduled(new \DateTime());
    }
}
