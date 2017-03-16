<?php

namespace Task\TaskBundle\Unit\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Task\Scheduler\TaskSchedulerInterface;
use Task\Storage\TaskExecutionRepositoryInterface;
use Task\TaskBundle\Builder\TaskBuilder;
use Task\TaskBundle\Command\ScheduleSystemTasksCommand;
use Task\TaskBundle\Entity\TaskRepository;
use Task\TaskBundle\Tests\Functional\TestHandler;

class ScheduleSystemTasksCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskSchedulerInterface
     */
    private $scheduler;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var TaskExecutionRepositoryInterface
     */
    private $taskExecutionRepository;

    protected function setUp()
    {
        $this->scheduler = $this->prophesize(TaskSchedulerInterface::class);
        $this->taskRepository = $this->prophesize(TaskRepository::class);
        $this->taskExecutionRepository = $this->prophesize(TaskExecutionRepositoryInterface::class);
    }

    /**
     * @param array $systemTasks
     *
     * @return ScheduleSystemTasksCommand
     */
    protected function createCommand(array $systemTasks)
    {
        return new ScheduleSystemTasksCommand(
            'task:schedule:system-tasks',
            $systemTasks,
            $this->scheduler->reveal(),
            $this->taskRepository->reveal(),
            $this->taskExecutionRepository->reveal()
        );
    }

    public function testExecute()
    {
        $command = $this->createCommand(
            [
                'testing' => [
                    'enabled' => true,
                    'handler_class' => TestHandler::class,
                    'workload' => 'test',
                    'cron_expression' => '* * * * *',
                ],
            ]
        );

        $taskBuilder = $this->prophesize(TaskBuilder::class);

        $this->taskRepository->findBySystemKey('testing')->willReturn(null);
        $this->scheduler->createTask(TestHandler::class, 'test')->shouldBeCalled()->willReturn($taskBuilder->reveal());

        $taskBuilder->setSystemKey('testing')->shouldBeCalled();
        $taskBuilder->cron('* * * * *')->shouldBeCalled();
        $taskBuilder->schedule()->shouldBeCalled();

        $command->run(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal()
        );
    }

    public function testExecuteMultiple()
    {
        $command = $this->createCommand(
            [
                'testing-1' => [
                    'enabled' => true,
                    'handler_class' => TestHandler::class,
                    'workload' => 'test-1',
                    'cron_expression' => '* * * * *',
                ],
                'testing-2' => [
                    'enabled' => true,
                    'handler_class' => TestHandler::class,
                    'workload' => 'test-2',
                    'cron_expression' => '* * * * *',
                ],
            ]
        );

        $this->taskRepository->findBySystemKey('testing-1')->willReturn(null);
        $this->taskRepository->findBySystemKey('testing-2')->willReturn(null);

        $taskBuilder1 = $this->prophesize(TaskBuilder::class);
        $this->scheduler->createTask(TestHandler::class, 'test-1')->shouldBeCalled()->willReturn($taskBuilder1->reveal());
        $taskBuilder1->setSystemKey('testing-1')->shouldBeCalled();
        $taskBuilder1->cron('* * * * *')->shouldBeCalled();
        $taskBuilder1->schedule()->shouldBeCalled();

        $taskBuilder2 = $this->prophesize(TaskBuilder::class);
        $this->scheduler->createTask(TestHandler::class, 'test-2')->shouldBeCalled()->willReturn($taskBuilder2->reveal());
        $taskBuilder2->setSystemKey('testing-2')->shouldBeCalled();
        $taskBuilder2->cron('* * * * *')->shouldBeCalled();
        $taskBuilder2->schedule()->shouldBeCalled();

        $command->run(
            $this->prophesize(InputInterface::class)->reveal(),
            $this->prophesize(OutputInterface::class)->reveal()
        );
    }

    // TODO Tests for update and disable.
}
