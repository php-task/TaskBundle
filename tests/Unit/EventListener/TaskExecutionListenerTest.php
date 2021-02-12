<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Task\Event\TaskExecutionEvent;
use Task\TaskBundle\EventListener\DoctrineTaskExecutionListener;

/**
 * Tests for class TaskExecutionListener.
 */
class TaskExecutionListenerTest extends TestCase
{
    public function testClearEntityManagerAfterTask()
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $listener = new DoctrineTaskExecutionListener($entityManager->reveal());
        $listener->clearEntityManagerAfterTask($this->prophesize(TaskExecutionEvent::class)->reveal());

        $entityManager->clear()->shouldBeCalled();
    }
}
