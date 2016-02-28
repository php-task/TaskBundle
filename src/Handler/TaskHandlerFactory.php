<?php

namespace Task\TaskBundle\Handler;

use Symfony\Component\CssSelector\Parser\Handler\HandlerInterface;
use Task\Handler\TaskHandlerFactoryInterface;
use Task\Handler\TaskHandlerNotExistsException;

class TaskHandlerFactory implements TaskHandlerFactoryInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handler = [];

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
