<?php

namespace Unit\DependencyInjection;

use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Task\TaskBundle\DependencyInjection\HandlerCompilerPass;

class HandlerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $containerBuilder = $this->prophesize(ContainerBuilder::class);
        $definition = $this->prophesize(Definition::class);

        $containerBuilder->has(HandlerCompilerPass::REGISTRY_ID)->willReturn(true);
        $containerBuilder->findDefinition(HandlerCompilerPass::REGISTRY_ID)->willReturn($definition->reveal());

        $containerBuilder->findTaggedServiceIds(HandlerCompilerPass::HANDLER_TAG)
            ->willReturn(
                [
                    'id-1' => [
                        ['handler-name' => 'name-1'],
                    ],
                    'id-2' => [
                        ['handler-name' => 'name-2-1'],
                        ['handler-name' => 'name-2-2'],
                    ],
                ]
            );

        $compilerPass = new HandlerCompilerPass();
        $compilerPass->process($containerBuilder->reveal());

        $definition->addMethodCall(
            HandlerCompilerPass::ADD_FUNCTION_NAME,
            Argument::that(
                function ($arguments) {
                    return $arguments[0] === 'name-1' && $arguments[1]->__toString() === 'id-1';
                }
            )
        )->shouldBeCalledTimes(1);
        $definition->addMethodCall(
            HandlerCompilerPass::ADD_FUNCTION_NAME,
            Argument::that(
                function ($arguments) {
                    return $arguments[0] === 'name-2-1' && $arguments[1]->__toString() === 'id-2';
                }
            )
        )->shouldBeCalledTimes(1);
        $definition->addMethodCall(
            HandlerCompilerPass::ADD_FUNCTION_NAME,
            Argument::that(
                function ($arguments) {
                    return $arguments[0] === 'name-2-2' && $arguments[1]->__toString() === 'id-2';
                }
            )
        )->shouldBeCalledTimes(1);
    }
}
