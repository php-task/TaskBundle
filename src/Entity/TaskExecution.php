<?php

namespace Task\TaskBundle\Entity;

use Task\Execution\TaskExecution as BaseTaskExecution;

class TaskExecution extends BaseTaskExecution
{
    /**
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
