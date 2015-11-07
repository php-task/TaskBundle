<?php

namespace Task\TaskBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass which collects worker services.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class WorkerCompilerPass implements CompilerPassInterface
{
    const TASK_RUNNER_ID = 'task.runner';
    const WORKER_TAG = 'task.worker';
    const ADD_FUNCTION_NAME = 'addWorker';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::TASK_RUNNER_ID)) {
            return;
        }

        $definition = $container->findDefinition(self::TASK_RUNNER_ID);

        $taggedServices = $container->findTaggedServiceIds(self::WORKER_TAG);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(self::ADD_FUNCTION_NAME, array(new Reference($id)));
        }
    }
}
