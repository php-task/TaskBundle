<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Task\Event\TaskExecutionEvent;

/**
 * Listens on task-execution events.
 */
class TaskExecutionListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This method clears the entity-manager after each task to ensure clean state before next task.
     *
     * @param TaskExecutionEvent $event
     */
    public function clearEntityManagerAfterTask(TaskExecutionEvent $event)
    {
        if (!$this->entityManager) {
            return;
        }

        $this->entityManager->clear();
    }
}
