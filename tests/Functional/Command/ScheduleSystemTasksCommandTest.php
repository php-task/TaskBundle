<?php

namespace Task\TaskBundle\Functional\Command;

use Symfony\Component\Console\Command\Command;
use Task\TaskBundle\Tests\Functional\BaseCommandTestCase;

class ScheduleSystemTasksCommandTest extends BaseCommandTestCase
{
    public function testExecute()
    {
        if (self::$kernel->getContainer()->getParameter('kernel.storage') !== 'doctrine') {
            return $this->markTestSkipped('This testcase will only be called for doctrine storage.');
        }

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertContains('System-tasks successfully scheduled', $output);

        $taskRepository = self::$kernel->getContainer()->get('task.repository.task');
        $this->assertNotNull($taskRepository->findBySystemKey('testing'));

    }

    /**
     * Returns command.
     *
     * @return Command
     */
    protected function getCommand()
    {
        return self::$kernel->getContainer()->get('task.command.schedule_system_tasks');
    }
}
