<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass which collects worker services.
 */
class HandlerCompilerPass implements CompilerPassInterface
{
    const REGISTRY_ID = 'task.handler.factory';
    const HANDLER_TAG = 'task.handler';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::REGISTRY_ID)) {
            return;
        }

        $handler = [];
        $taggedServices = $container->findTaggedServiceIds(self::HANDLER_TAG);
        foreach ($taggedServices as $id => $tags) {
            $service = $container->getDefinition($id);
            $handler[$service->getClass()] = new Reference($id);
        }

        $definition = $container->findDefinition(self::REGISTRY_ID);
        $definition->replaceArgument(0, $handler);
    }
}
