<?php

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\SchedulerInterface;
use Task\Storage\ArrayStorage;
use Task\TaskBundle\Storage\DoctrineStorage;

class BootstrapTest extends KernelTestCase
{
    public function testBootstrap()
    {
        $this->bootKernel();

        $scheduler = self::$kernel->getContainer()->get('task.scheduler');
        $storage = self::$kernel->getContainer()->get('task.storage');

        $this->assertInstanceOf(SchedulerInterface::class, $scheduler);

        switch (self::$kernel->getContainer()->getParameter('kernel.storage')) {
            case 'array':
                $this->assertInstanceOf(ArrayStorage::class, $storage);
                break;
            case 'doctrine':
                $this->assertInstanceOf(DoctrineStorage::class, $storage);
                break;
            default:
                $this->fail('storage not supported');
                break;
        }
    }
}
