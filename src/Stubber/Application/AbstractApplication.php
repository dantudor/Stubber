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
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
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
