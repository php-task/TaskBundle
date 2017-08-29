<?php

namespace Task\TaskBundle\Executor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

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
     * @param string $consolePath
     * @param string $environment
     */
    public function __construct($consolePath, $environment)
    {
        $this->consolePath = $consolePath;
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
        return $process = ProcessBuilder::create(
            [$this->consolePath, 'task:execute', $uuid, '--env=' . $this->environment]
        )->getProcess();
    }
}
