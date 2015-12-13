<?php

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HandlerTest extends KernelTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();
    }

    public function testHas()
    {
        $registry = self::$kernel->getContainer()->get('task.handler_registry');

        $this->assertTrue($registry->has('test'));
    }

    public function testRun()
    {
        $registry = self::$kernel->getContainer()->get('task.handler_registry');

        $this->assertEquals('daolkrow', $registry->run('test', 'workload'));
    }
}
