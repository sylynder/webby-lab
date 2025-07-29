<?php

namespace App\Packages\Command\Services;

use GO\Scheduler as Go;

class Scheduler extends Go
{
    /**
     * Create new instance.
     *
     * @param  array  $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Queue a raw shell command.
     *
     * @param  string  $command  The command to execute
     * @param  array  $args      Optional arguments to pass to the command
     * @param  string  $id       Optional custom identifier
     * @return mixed
     */
    public function command($command, $args = [], $id = null)
    {
        return $this->raw($command, $args, $id);
    }
}
