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

use Symfony\Component\CssSelector\Parser\Handler\HandlerInterface;
use Task\Handler\TaskHandlerFactoryInterface;
use Task\Handler\TaskHandlerNotExistsException;

/**
 * Uses symfony container for collecting handler.
 */
class TaskHandlerFactory implements TaskHandlerFactoryInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handler = [];

    /**
     * @param array $handler
     */
    public function __construct(array $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function create($className)
    {
        if (!array_key_exists($className, $this->handler)) {
            throw new TaskHandlerNotExistsException($className);
        }

        return $this->handler[$className];
    }
}
