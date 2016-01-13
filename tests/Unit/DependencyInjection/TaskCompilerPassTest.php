<?php

namespace Unit\DependencyInjection;

use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Task\TaskBundle\DependencyInjection\HandlerCompilerPass;
use Task\TaskBundle\DependencyInjection\TaskCompilerPass;

class TaskCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $containerBuilder = $this->prophesize(ContainerBuilder::class);
        $schedulerDefinition = $this->prophesize(Definition::class);
        $handler1Definition = $this->prophesize(Definition::class);
        $handler2Definition = $this->prophesize(Definition::class);

        $containerBuilder->has(TaskCompilerPass::SCHEDULER_ID)->willReturn(true);
        $containerBuilder->getDefinition(TaskCompilerPass::SCHEDULER_ID)->willReturn($schedulerDefinition->reveal());
        $containerBuilder->getDefinition('id-1')->willReturn($handler1Definition->reveal());
        $containerBuilder->getDefinition('id-2')->willReturn($handler2Definition->reveal());

        $containerBuilder->findTaggedServiceIds(TaskCompilerPass::INTERVAL_TAG)
            ->willReturn(
                [
                    'id-1' => [
                        [
                            TaskCompilerPass::INTERVAL_ATTRIBUTE => 'daily',
                            TaskCompilerPass::WORKLOAD_ATTRIBUTE => 'test-workload',
                            TaskCompilerPass::KEY_ATTRIBUTE => 'test-key'
                        ],
                    ],
                    'id-2' => [
                        [
                            TaskCompilerPass::INTERVAL_ATTRIBUTE => 'daily',
                            TaskCompilerPass::KEY_ATTRIBUTE => 'test-key-1'
                        ],
                        [
                            TaskCompilerPass::INTERVAL_ATTRIBUTE => 'daily',
                            TaskCompilerPass::WORKLOAD_ATTRIBUTE => 'test-workload-2'
                        ],
                    ],
                ]
            );

        $handler1Definition->getTag(HandlerCompilerPass::HANDLER_TAG)->willReturn(
            [[HandlerCompilerPass::HANDLER_NAME_ATTRIBUTE => 'handler-1']]
        );
        $handler2Definition->getTag(HandlerCompilerPass::HANDLER_TAG)->willReturn(
            [[HandlerCompilerPass::HANDLER_NAME_ATTRIBUTE => 'handler-2']]
        );

        $compilerPass = new TaskCompilerPass();
        $compilerPass->process($containerBuilder->reveal());

        $schedulerDefinition->addMethodCall(
            TaskCompilerPass::CREATE_FUNCTION_NAME,
            Argument::that(
                function ($arguments) {
                    return
                        $arguments[0] === 'handler-1'
                        && $arguments[1] === 'test-workload'
                        && $arguments[2] === 'daily'
                        && $arguments[3] === 'test-key';
                }
            )
        )->shouldBeCalledTimes(1);
        $schedulerDefinition->addMethodCall(
            TaskCompilerPass::CREATE_FUNCTION_NAME,
            Argument::that(
                function ($arguments) {
                    return
                        $arguments[0] === 'handler-2'
                        && $arguments[1] === null
                        && $arguments[2] === 'daily'
                        && $arguments[3] === 'test-key';
                }
            )
        )->shouldBeCalledTimes(1);
        $schedulerDefinition->addMethodCall(
            TaskCompilerPass::CREATE_FUNCTION_NAME,
            Argument::that(
                function ($arguments) {
                    return
                        $arguments[0] === 'handler-2'
                        && $arguments[1] === 'test-workload-2'
                        && $arguments[2] === 'daily'
                        && $arguments[3] === 'handler-2_daily_s:15:"test-workload-2";';
                }
            )
        )->shouldBeCalledTimes(1);

        // TODO this test always returns true should be extended
    }
}
