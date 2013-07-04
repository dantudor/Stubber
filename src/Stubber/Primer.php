<?php

namespace Stubber;

use Pagon\ChildProcess\Process;

/**
 * Class Primer
 *
 * @package Stubber
 */
class Primer
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var Process
     */
    protected $process;

    /**
     * Set Process
     *
     * @param Process $process
     *
     * @return Primer
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get Process
     *
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set Server
     *
     * @param Server $server
     *
     * @return Primer
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get Server
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }
}