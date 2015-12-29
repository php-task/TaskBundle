<?php

namespace Task\TaskBundle\Entity;

use Task\Task as BaseTask;

class Task extends BaseTask
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
