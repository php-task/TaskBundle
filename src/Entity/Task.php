<?php

namespace Task\TaskBundle\Entity;

use Cron\CronExpression;
use Task\Task as BaseTask;

class Task extends BaseTask
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $intervalExpression;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIntervalExpression()
    {
        return $this->intervalExpression;
    }

    public function getInterval()
    {
        if (null === $this->interval && null !== $this->intervalExpression) {
            $this->interval = CronExpression::factory($this->intervalExpression);
        }

        return parent::getInterval();
    }

    /**
     * {@inheritdoc}
     */
    public function setInterval(CronExpression $interval, \DateTime $firstExecution = null, \DateTime $lastExecution = null)
    {
        parent::setInterval($interval, $firstExecution, $lastExecution);

        $this->intervalExpression = $interval->getExpression();
    }
}
