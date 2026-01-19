<?php

namespace Task\TaskBundle\Tests\Functional\Command;

use Task\Execution\TaskExecutionInterface;
use Task\Storage\TaskExecutionRepositoryInterface;
use Task\Storage\TaskRepositoryInterface;
use Task\TaskBundle\Entity\Task;
use Task\TaskBundle\Tests\Functional\BaseDatabaseTestCase;
use Task\TaskInterface;
use Task\TaskStatus;

/**
 * Tests for TaskExecutionRepository.
 */
class TaskExecutionRepositoryTest extends BaseDatabaseTestCase
{
    /**
     * @var TaskExecutionRepositoryInterface
     */
    protected $taskExecutionRepository;

    /**
     * @var TaskRepositoryInterface
     */
    protected $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskExecutionRepository = self::$kernel->getContainer()->get('task.storage.task_execution');
        $this->taskRepository = self::$kernel->getContainer()->get('task.storage.task');
    }

    public function testSave()
    {
        $execution = $this->save();

        $result = $this->taskExecutionRepository->findByUuid($execution->getUuid());

        $this->assertEquals($execution->getUuid(), $result->getUuid());
        $this->assertEquals($execution->getScheduleTime(), $result->getScheduleTime());
        $this->assertEquals($execution->getStatus(), $result->getStatus());
        $this->assertEquals($execution->getTask()->getUuid(), $result->getTask()->getUuid());
    }

    public function testRemove()
    {
        $execution = $this->save();

        $this->taskExecutionRepository->remove($execution);

        $this->assertNull($this->taskExecutionRepository->findByUuid($execution->getUuid()));
    }

    public function testFindAll()
    {
        $task = $this->createTask();
        $this->taskRepository->save($task);

        $executions = [];
        for ($i = 0; $i < 3; ++$i) {
            $execution = $this->save($task);
            $executions[$execution->getUuid()] = $execution;
        }

        $result = $this->taskExecutionRepository->findAll();

        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertArrayHasKey($item->getUuid(), $executions);
            unset($executions[$item->getUuid()]);
        }
    }

    public function testFindAllPaginated()
    {
        $task = $this->createTask();
        $this->taskRepository->save($task);

        $executions = [];
        for ($i = 0; $i < 3; ++$i) {
            $execution = $this->save($task);
            $executions[$execution->getUuid()] = $execution;
        }

        $result = $this->taskExecutionRepository->findAllPaginated(1, 2);

        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertArrayHasKey($item->getUuid(), $executions);
            unset($executions[$item->getUuid()]);
        }

        $result = $this->taskExecutionRepository->findAllPaginated(2, 2);

        $this->assertCount(1, $result);
        foreach ($result as $item) {
            $this->assertArrayHasKey($item->getUuid(), $executions);
            unset($executions[$item->getUuid()]);
        }

        $this->assertEmpty($executions);
    }

    public function testFindPending()
    {
        $execution = $this->save();
        $this->assertNotNull($this->taskExecutionRepository->findPending($execution->getTask()));

        $execution = $this->save(null, null, TaskStatus::RUNNING);
        $this->assertNotNull($this->taskExecutionRepository->findPending($execution->getTask()));

        $execution = $this->save(null, null, TaskStatus::COMPLETED);
        $this->assertNull($this->taskExecutionRepository->findPending($execution->getTask()));

        $execution = $this->save(null, null, TaskStatus::FAILED);
        $this->assertNull($this->taskExecutionRepository->findPending($execution->getTask()));
    }

    public function testFindByUuid()
    {
        $execution = $this->save();

        $result = $this->taskExecutionRepository->findByUuid($execution->getUuid());
        $this->assertEquals($execution->getUuid(), $result->getUuid());
    }

    public function testFindByTask()
    {
        $execution = $this->save();

        $result = $this->taskExecutionRepository->findByTask($execution->getTask());

        $this->assertCount(1, $result);
        $this->assertEquals($execution->getTask()->getUuid(), $result[0]->getTask()->getUuid());
    }

    public function testFindByTaskUuid()
    {
        $execution = $this->save();

        $result = $this->taskExecutionRepository->findByTaskUuid($execution->getTask()->getUuid());

        $this->assertCount(1, $result);
        $this->assertEquals($execution->getTask()->getUuid(), $result[0]->getTask()->getUuid());
    }

    public function testFindScheduledPast()
    {
        $task = $this->createTask();
        $this->taskRepository->save($task);

        $execution = $this->save($task, new \DateTime('-1 hour'));

        $result = $this->taskExecutionRepository->findNextScheduled();
        $this->assertEquals($execution->getUuid(), $result->getUuid());
    }

    public function testFindScheduledFuture()
    {
        $task = $this->createTask();
        $this->taskRepository->save($task);

        $this->save($task, new \DateTime('+1 hour'));

        $this->assertNull($this->taskExecutionRepository->findNextScheduled());
    }

    public function testFindScheduledSkipped()
    {
        $task = $this->createTask();
        $this->taskRepository->save($task);

        $this->save($task, new \DateTime('+1 hour'));

        $this->assertNull($this->taskExecutionRepository->findNextScheduled());
    }

    /**
     * Save a new execution to database.
     *
     * @param TaskInterface $task
     * @param \DateTime $scheduleTime
     * @param string $status
     *
     * @return TaskExecutionInterface
     */
    private function save(TaskInterface $task = null, \DateTime $scheduleTime = null, $status = TaskStatus::PLANNED)
    {
        if (!$scheduleTime) {
            $scheduleTime = new \DateTime();
        }

        if (!$task) {
            $task = $this->createTask();
            $this->taskRepository->save($task);
        }

        $execution = $this->createTaskExecution($task, $scheduleTime, $status);
        $this->taskExecutionRepository->save($execution);

        return $execution;
    }
}
