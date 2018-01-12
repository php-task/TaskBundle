<?php

/*
 * This file is part of php-task library.
 *
 * (c) php-task
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Task\TaskBundle\Executor;

/**
 * Exception wrapper which transports the serialized exception from console to the runner.
 */
class SeparateProcessException extends \Exception
{
    /**
     * @var string
     */
    private $errorOutput;

    /**
     * @param string $errorOutput
     */
    public function __construct($errorOutput)
    {
        parent::__construct($errorOutput);

        $this->errorOutput = $errorOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->errorOutput;
    }
}
