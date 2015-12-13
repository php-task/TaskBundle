<?php

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\SchedulerInterface;
use Task\Storage\ArrayStorage;

class BootstrapTest extends KernelTestCase
{
    public function testBootstrap()
    {
        $this->bootKernel();

        $scheduler = self::$kernel->getContainer()->get('task.scheduler');
        $storage = self::$kernel->getContainer()->get('task.storage');

        $this->assertInstanceOf(SchedulerInterface::class, $scheduler);

        switch (getenv(\TestKernel::STORAGE_VAR_NAME)) {
            case 'array':
                $this->assertInstanceOf(ArrayStorage::class, $storage);
                break;
            default:
                $this->fail('storage not supported');
                break;
        }
    }
}
