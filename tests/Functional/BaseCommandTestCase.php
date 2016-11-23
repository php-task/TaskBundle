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

use Cron\CronExpression;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Task\Execution\TaskExecutionInterface;
use Task\Runner\TaskRunnerInterface;
use Task\Scheduler\TaskSchedulerInterface;
use Task\Storage\TaskExecutionRepositoryInterface;
use Task\Storage\TaskRepositoryInterface;
use Task\TaskInterface;
use Task\TaskStatus;

/**
 * Base class for testing commands.
 */
abstract class BaseCommandTestCase extends KernelTestCase
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var TaskRepositoryInterface
     */
    protected $taskRepository;

    /**
     * @var TaskExecutionRepositoryInterface
     */
    protected $taskExecutionRepository;

    /**
     * @var TaskSchedulerInterface
     */
    protected $taskScheduler;

    /**
     * @var TaskRunnerInterface
     */
    protected $taskRunner;

    /**
     * @var Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        self::bootKernel();

        $this->taskRunner = self::$kernel->getContainer()->get('task.runner');
        $this->taskScheduler = self::$kernel->getContainer()->get('task.scheduler');
        $this->taskRepository = self::$kernel->getContainer()->get('task.storage.task');
        $this->taskExecutionRepository = self::$kernel->getContainer()->get('task.storage.task_execution');

        $command = $this->getCommand();

        $this->application = new Application(self::$kernel);
        $this->application->add($command);

        $this->command = $this->application->find($command->getName());
        $this->commandTester = new CommandTester($this->command);

        $this->purgeDatabase();
    }

    /**
     * Create new task.
     *
     * @param string $workload
     * @param CronExpression $cronExpression
     * @param string $handlerClass
     *
     * @return TaskInterface
     */
    protected function createTask($workload, CronExpression $cronExpression = null, $handlerClass = TestHandler::class)
    {
        $task = $this->taskRepository->create($handlerClass, $workload);
        if ($cronExpression) {
            $task->setInterval($cronExpression, new \DateTime(), new \DateTime('+1 year'));
        }
        $this->taskRepository->save($task);

        return $task;
    }

    /**
     * Create task-execution.
     *
     * @param TaskInterface $task
     * @param \DateTime $scheduleTime
     * @param string $status
     *
     * @return TaskExecutionInterface
     */
    protected function createTaskExecution(TaskInterface $task, \DateTime $scheduleTime, $status = TaskStatus::PLANNED)
    {
        $execution = $this->taskExecutionRepository->create($task, $scheduleTime);
        $execution->setStatus($status);
        $this->taskExecutionRepository->save($execution);

        $this->getEntityManager()->flush();

        return $execution;
    }

    /**
     * Purge the Doctrine ORM database.
     */
    protected function purgeDatabase()
    {
        if (!self::$kernel->getContainer()->has('doctrine')) {
            return;
        }

        $manager = $this->getEntityManager();
        $connection = $manager->getConnection();

        if ($connection->getDriver() instanceof Driver) {
            $connection->executeUpdate('SET foreign_key_checks = 0;');
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($manager, $purger);
        $referenceRepository = new ProxyReferenceRepository($manager);
        $executor->setReferenceRepository($referenceRepository);
        $executor->purge();

        if ($connection->getDriver() instanceof Driver) {
            $connection->executeUpdate('SET foreign_key_checks = 1;');
        }
    }

    /**
     * Returns entity-manager.
     *
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Returns command.
     *
     * @return Command
     */
    abstract protected function getCommand();
}
