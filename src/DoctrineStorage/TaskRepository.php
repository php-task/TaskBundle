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
use Task\Storage\TaskRepositoryInterface;
use Task\TaskBundle\Entity\TaskRepository as ORMTaskRepository;
use Task\TaskInterface;

/**
 * Task storage which uses doctrine.
 */
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
     * @param ObjectManager $objectManager
     * @param ORMTaskRepository $taskRepository
     */
    public function __construct(ObjectManager $objectManager, ORMTaskRepository $taskRepository)
    {
        $this->objectManager = $objectManager;
        $this->taskRepository = $taskRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function store(TaskInterface $task)
    {
        $this->objectManager->persist($task);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($limit = null)
    {
        return $this->taskRepository->findBy([], null, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function findEndBeforeNow()
    {
        return $this->taskRepository->findEndBefore(new \DateTime());
    }
}
