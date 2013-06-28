<?php

namespace Stubber\Application;

use Stubber\Server;
use React\Http\Request;
use React\Http\Response;

/**
 * Class AbstractApplication
 *
 * @package Stubber\Application
 */
abstract class AbstractApplication
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var Server
     */
    protected $server;

    /**
     * Constructor
     *
     * @param string $host Host
     * @param int    $port Port
     * @param Server $server
     */
    public function __construct($host, $port, Server $server)
    {
        $this->host = $host;
        $this->port = $port;
        $this->server = $server;
    }

    /**
     * Get Host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get Port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
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

    /**
     * Run Application
     *
     * @return AbstractApplication
     */
    public function run()
    {
        $application = $this;
        $this->server->getHttpServer()->on('request', function ($request, $response) use ($application) {
            $application->handleRequest($request, $response);
        });

        $this->server->start($this->host, $this->port);

        return $this;
    }

    /**
     * Handle Request
     *
     * @param Request  $request  Request
     * @param Response $response Response
     */
    abstract public function handleRequest(Request $request, Response $response);
}
