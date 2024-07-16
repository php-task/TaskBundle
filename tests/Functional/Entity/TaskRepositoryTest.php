<?php

namespace Task\TaskBundle\Tests\Functional\Entity;

use Task\TaskBundle\Entity\TaskRepository;
use Task\TaskBundle\Tests\Functional\BaseDatabaseTestCase;

class TaskRepositoryTest extends BaseDatabaseTestCase
{
    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = self::$kernel->getContainer()->get('task.storage.task');
    }

    public function testFindBySystemKey()
    {
        if ('doctrine' !== self::$kernel->getContainer()->getParameter('kernel.storage')) {
            return $this->markTestSkipped('This testcase will only be called for doctrine storage.');
        }

        $task = $this->createTask();
        $task->setSystemKey('test');

        $this->taskRepository->save($task);

        $result = $this->taskRepository->findBySystemKey('test');
        $this->assertEquals($task->getUuid(), $result->getUuid());
    }

    public function testFindBySystemKeyNotFound()
    {
        if ('doctrine' !== self::$kernel->getContainer()->getParameter('kernel.storage')) {
            return $this->markTestSkipped('This testcase will only be called for doctrine storage.');
        }

        $task = $this->createTask();
        $this->taskRepository->save($task);

        $this->assertNull($this->taskRepository->findBySystemKey('test'));
    }

    public function testFindSystemTasks()
    {
        if ('doctrine' !== self::$kernel->getContainer()->getParameter('kernel.storage')) {
            return $this->markTestSkipped('This testcase will only be called for doctrine storage.');
        }

        $task1 = $this->createTask();
        $task1->setSystemKey('test');
        $this->taskRepository->save($task1);

        $task2 = $this->createTask();
        $this->taskRepository->save($task2);

        $result = $this->taskRepository->findSystemTasks();
        $this->assertCount(1, $result);
        $this->assertEquals($task1->getUuid(), $result[0]->getUuid());
    }
}
