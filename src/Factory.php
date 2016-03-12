<?php

namespace Task\TaskBundle;

use Task\TaskBundle\Entity\Task;

class Factory extends \Task\Factory
{
    /**
     * {@inheritdoc}
     */
    public function createTask($handlerClass, $workload)
    {
        return new Task($handlerClass, $workload);
    }
}
