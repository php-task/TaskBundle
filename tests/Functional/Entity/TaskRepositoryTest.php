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

    public function setUp()
    {
        parent::setUp();

        $this->taskRepository = self::$kernel->getContainer()->get('task.storage.task');
    }

    public function testFinBySystemKey()
    {
        if (self::$kernel->getContainer()->getParameter('kernel.storage') !== 'doctrine') {
            return $this->markTestSkipped('This testcase will only be called for doctrine storage.');
        }

        $task = $this->createTask();
        $task->setSystemKey('test');

        $this->taskRepository->save($task);

        $result = $this->taskRepository->findBySystemKey('test');
        $this->assertEquals($task->getUuid(), $result->getUuid());
    }

    public function testFinBySystemKeyNotFound()
    {
        if (self::$kernel->getContainer()->getParameter('kernel.storage') !== 'doctrine') {
            return $this->markTestSkipped('This testcase will only be called for doctrine storage.');
        }

        $task = $this->createTask();
        $this->taskRepository->save($task);

        $this->assertNull($this->taskRepository->findBySystemKey('test'));
    }
}
