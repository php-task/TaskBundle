<?php

namespace Task\TaskBundle\Tests\Functional;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\TaskBundle\Entity\Task;
use Task\TaskBundle\Entity\TaskExecution;
use Task\TaskInterface;
use Task\TaskStatus;

/**
 * Extends kernel-test-case with additional functions/properties.
 */
abstract class BaseDatabaseTestCase extends KernelTestCase
{
    const ENTITY_MANAGER_ID = 'doctrine.orm.entity_manager';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->purgeDatabase();
    }

    /**
     * Purges database is necessary.
     */
    protected function purgeDatabase()
    {
        if (!self::$kernel->getContainer()->has(self::ENTITY_MANAGER_ID)) {
            return;
        }

        /** @var EntityManagerInterface $manager */
        $manager = self::$kernel->getContainer()->get(self::ENTITY_MANAGER_ID);
        $connection = $manager->getConnection();

        if ($connection->getDriver() instanceof \Doctrine\DBAL\Driver\PDOMySql\Driver) {
            $connection->executeUpdate('SET foreign_key_checks = 0;');
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($manager, $purger);
        $referenceRepository = new ProxyReferenceRepository($manager);
        $executor->setReferenceRepository($referenceRepository);
        $executor->purge();

        if ($connection->getDriver() instanceof \Doctrine\DBAL\Driver\PDOMySql\Driver) {
            $connection->executeUpdate('SET foreign_key_checks = 1;');
        }
    }

    /**
     * Create a new task.
     *
     * @param string $handlerClass
     *
     * @return Task
     */
    protected function createTask($handlerClass = TestHandler::class)
    {
        return new Task($handlerClass);
    }

    /**
     * Create a new task-execution.
     *
     * @param TaskInterface $task
     * @param \DateTimeImmutable $scheduleTime
     * @param string $status
     *
     * @return TaskExecution
     */
    protected function createTaskExecution(TaskInterface $task, \DateTimeImmutable $scheduleTime, $status = TaskStatus::PLANNED)
    {
        $execution = new TaskExecution($task, $task->getHandlerClass(), $scheduleTime);
        $execution->setStatus($status);

        return $execution;
    }
}
