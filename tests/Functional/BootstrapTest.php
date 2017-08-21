<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\Scheduler\TaskSchedulerInterface;
use Task\Storage\ArrayStorage\ArrayTaskExecutionRepository;
use Task\Storage\ArrayStorage\ArrayTaskRepository;
use Task\TaskBundle\Entity\TaskExecutionRepository;
use Task\TaskBundle\Entity\TaskRepository;

/**
 * Tests the service definitions.
 */
class BootstrapTest extends KernelTestCase
{
    public function testBootstrap()
    {
        $this->bootKernel();

        $scheduler = self::$kernel->getContainer()->get('task.scheduler');
        $taskRepository = self::$kernel->getContainer()->get('task.storage.task');
        $taskExecutionRepository = self::$kernel->getContainer()->get('task.storage.task_execution');

        $this->assertInstanceOf(TaskSchedulerInterface::class, $scheduler);

        switch (self::$kernel->getContainer()->getParameter('kernel.storage')) {
            case 'array':
                $this->assertInstanceOf(ArrayTaskRepository::class, $taskRepository);
                $this->assertInstanceOf(ArrayTaskExecutionRepository::class, $taskExecutionRepository);

                break;
            case 'doctrine':
                $this->assertInstanceOf(TaskRepository::class, $taskRepository);
                $this->assertInstanceOf(TaskExecutionRepository::class, $taskExecutionRepository);

                break;
            default:
                $this->fail('storage not supported');

                break;
        }
    }
}
