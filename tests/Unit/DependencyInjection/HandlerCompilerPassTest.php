<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Task\TaskBundle\DependencyInjection\HandlerCompilerPass;

/**
 * Tests for class HandlerCompilerPass.
 */
class HandlerCompilerPassTest extends TestCase
{
    public function testProcess()
    {
        $container = $this->prophesize(ContainerBuilder::class);

        $container->has(HandlerCompilerPass::REGISTRY_ID)->willReturn(true);
        $container->findTaggedServiceIds(HandlerCompilerPass::HANDLER_TAG)->willReturn(
            ['service1' => [], 'service2' => []]
        );
        $container->getDefinition('service1')->willReturn(new Definition(\stdClass::class));
        $container->getDefinition('service2')->willReturn(new Definition(self::class));

        $serviceDefinition = $this->prophesize(Definition::class);
        $container->findDefinition(HandlerCompilerPass::REGISTRY_ID)->willReturn($serviceDefinition);

        $compilerPass = new HandlerCompilerPass();
        $compilerPass->process($container->reveal());

        $serviceDefinition->replaceArgument(
            0,
            [\stdClass::class => new Reference('service1'), self::class => new Reference('service2')]
        )->shouldBeCalled();
    }

    public function testProcessNoTaggedService()
    {
        $container = $this->prophesize(ContainerBuilder::class);

        $container->has(HandlerCompilerPass::REGISTRY_ID)->willReturn(true);
        $container->findTaggedServiceIds(HandlerCompilerPass::HANDLER_TAG)->willReturn([]);

        $serviceDefinition = $this->prophesize(Definition::class);
        $container->findDefinition(HandlerCompilerPass::REGISTRY_ID)->willReturn($serviceDefinition);

        $compilerPass = new HandlerCompilerPass();
        $compilerPass->process($container->reveal());

        $serviceDefinition->replaceArgument(0, [])->shouldBeCalled();
    }
}
