<?php

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Task\Handler\RegistryInterface;

class HandlerRegistryTest extends KernelTestCase
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    protected function setUp()
    {
        parent::setUp();

        $this->bootKernel();
        $this->registry = self::$kernel->getContainer()->get('task.handler_registry');
    }

    public function testHas()
    {
        $this->assertTrue($this->registry->has('test'));
        $this->assertFalse($this->registry->has('test-1'));
    }

    public function testRun()
    {
        $this->assertEquals('daolkrow', $this->registry->run('test', 'workload'));
    }
}
