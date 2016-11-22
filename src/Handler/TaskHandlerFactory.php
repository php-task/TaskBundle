<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Handler;

use Task\Handler\TaskHandlerFactoryInterface;
use Task\Handler\TaskHandlerInterface;
use Task\Handler\TaskHandlerNotExistsException;

/**
 * Uses symfony container for collecting handler.
 */
class TaskHandlerFactory implements TaskHandlerFactoryInterface
{
    /**
     * @var TaskHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @param TaskHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function create($className)
    {
        if (!array_key_exists($className, $this->handlers)) {
            throw new TaskHandlerNotExistsException($className);
        }

        return $this->handlers[$className];
    }

    /**
     * Returns all known handler.
     *
     * @return TaskHandlerInterface[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
