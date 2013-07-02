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
     * @var Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $serverHost;

    /**
     * @var int
     */
    protected $serverPort;

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
     * Set Server Host
     *
     * @param string $host
     *
     * @return Server
     */
    public function setServerHost($host)
    {
        $this->serverHost = $host;

        return $this;
    }

    /**
     * Set Server Port
     *
     * @param int $port
     *
     * @return Server
     */
    public function setServerPort($port)
    {
        $this->serverPort = $port;

        return $this;
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

        $this->server
            ->setHost($this->serverHost)
            ->setPort($this->serverPort)
            ->start()
        ;

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
