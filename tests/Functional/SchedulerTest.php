<?php

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Task\SchedulerInterface;
use Task\Storage\StorageInterface;
use Task\Task;

class SchedulerTest extends KernelTestCase
{
    /**
     * @var SchedulerInterface
     */
    private $scheduler;

    /**
     * @var StorageInterface
     */
    private $storage;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();
        $this->scheduler = self::$kernel->getContainer()->get('task.scheduler');
        $this->storage = self::$kernel->getContainer()->get('task.storage');
    }

    public function testSchedule()
    {
        $this->storage->clear();

        $this->scheduler->schedule(new Task('test', 'workload'));

        $scheduled = $this->storage->findScheduled();
        $this->assertCount(1, $scheduled);
        $this->assertEquals('test', $scheduled[0]->getTaskName());
        $this->assertEquals('workload', $scheduled[0]->getWorkload());
        $this->assertFalse($scheduled[0]->isCompleted());
    }

    public function testRun()
    {
        $this->storage->clear();

        $this->scheduler->schedule(new Task('test', 'workload'));
        $this->scheduler->run();

        $scheduled = $this->storage->findScheduled();
        $this->assertCount(0, $scheduled);

        $all = $this->storage->findAll();
        $this->assertCount(1, $all);
        $this->assertEquals('test', $all[0]->getTaskName());
        $this->assertEquals('workload', $all[0]->getWorkload());
        $this->assertTrue($all[0]->isCompleted());
        $this->assertEquals('daolkrow', $all[0]->getResult());
    }

    // TODO daily
}
