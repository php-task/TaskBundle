<?php

namespace Task\TaskBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Task\SchedulerInterface;

/**
 * Listens to event and run scheduled tasks.
 *
 * @author @wachterjohannes <johannes.wachter@massiveart.com>
 */
class RunListener
{
    /**
     * @var SchedulerInterface
     */
    private $scheduler;

    public function __construct(SchedulerInterface $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    /**
     * Run scheduled tasks.
     *
     * @param Event $event
     */
    public function run(Event $event)
    {
        $this->scheduler->run();
    }
}
