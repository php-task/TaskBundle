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
class HandlerCompilerPass implements CompilerPassInterface
{
    const REGISTRY_ID = 'task.handler_registry';
    const HANDLER_TAG = 'task.handler';
    const ADD_FUNCTION_NAME = 'add';
    const HANDLER_NAME_ATTRIBUTE = 'handler-name';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::REGISTRY_ID)) {
            return;
        }

        $definition = $container->findDefinition(self::REGISTRY_ID);

        $taggedServices = $container->findTaggedServiceIds(self::HANDLER_TAG);
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    self::ADD_FUNCTION_NAME,
                    [$attributes[self::HANDLER_NAME_ATTRIBUTE], new Reference($id)]
                );
            }
        }
    }
}
