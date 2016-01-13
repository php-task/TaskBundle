<?php

namespace Task\TaskBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass which collects worker services.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class TaskCompilerPass implements CompilerPassInterface
{
    const SCHEDULER_ID = 'task.scheduler';
    const INTERVAL_TAG = 'task.interval';
    const KEY_ATTRIBUTE = 'key';
    const INTERVAL_ATTRIBUTE = 'interval';
    const WORKLOAD_ATTRIBUTE = 'workload';
    const CREATE_FUNCTION_NAME = 'createTaskAndSchedule';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SCHEDULER_ID)) {
            return;
        }

        $schedulerDefinition = $container->getDefinition(self::SCHEDULER_ID);

        $taggedServices = $container->findTaggedServiceIds(self::INTERVAL_TAG);
        foreach ($taggedServices as $id => $tags) {
            $handlerDefinition = $container->getDefinition($id);
            $tag = $handlerDefinition->getTag(HandlerCompilerPass::HANDLER_TAG);

            // TODO handle also multiple handler tag here
            $handler = $tag[0][HandlerCompilerPass::HANDLER_NAME_ATTRIBUTE];

            // remove all tasks with $id and not completed
            foreach ($tags as $attributes) {
                $interval = $attributes[self::INTERVAL_ATTRIBUTE];
                $workload = isset($attributes[self::WORKLOAD_ATTRIBUTE]) ? $attributes[self::WORKLOAD_ATTRIBUTE] : null;
                $key = isset($attributes[self::KEY_ATTRIBUTE]) ? $attributes[self::KEY_ATTRIBUTE] : null;

                if (!$key) {
                    $key = $handler . '_' . $interval . '_' . serialize($workload);
                }

                $schedulerDefinition->addMethodCall(
                    self::CREATE_FUNCTION_NAME,
                    [
                        $handler,
                        $workload,
                        $interval,
                        $key,
                    ]
                );
            }
        }
    }
}
