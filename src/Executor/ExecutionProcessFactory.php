<?php

namespace Task\TaskBundle\Executor;

use Symfony\Component\Process\Process;

/**
 * Factory for execution-process.
 */
class ExecutionProcessFactory
{
    /**
     * @var string
     */
    private $consolePath;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var float|null
     */
    private $processTimeout;

    /**
     * @param string $consolePath
     * @param float|null $processTimeout
     * @param string $environment
     */
    public function __construct($consolePath, $processTimeout, $environment)
    {
        $this->consolePath = $consolePath;
        $this->processTimeout = $processTimeout;
        $this->environment = $environment;
    }

    /**
     * Create process for given execution-uuid.
     *
     * @param string $uuid
     *
     * @return Process
     */
    public function create($uuid)
    {
        return $process = (new Process(
            implode(' ', [$this->consolePath, 'task:execute', $uuid, '--env=' . $this->environment])
        ))->setTimeout($this->processTimeout);
    }
}
