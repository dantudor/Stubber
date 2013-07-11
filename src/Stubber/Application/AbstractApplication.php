<?php

namespace Stubber\Application;

use Stubber\Server;
use React\Http\Request;
use React\Http\Response;
use Stubber\Primer\Request as PrimedRequest;
use Stubber\Exception\PrimerMissingException;
use Stubber\Exception\PrimerMethodMismatchException;
use Stubber\Exception\PrimerPathMismatchException;
use Stubber\Exception\PrimerQueryMismatchException;

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
        $this->server->setApplication($this);
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
     * @return $this
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
     * Get Expected Request
     *
     * @return null|PrimedRequest
     *
     * @throws PrimerMissingException
     */
    public function getExpectedRequest()
    {
        if (false === $this->getServer()->getPrimer()->isPrimed()) {
            throw new PrimerMissingException('The primed request was not found');
        }

        return $this->getServer()->getPrimer()->getNextPrimedRequest();
    }


    /**
     * Validate Request
     *
     * @param PrimedRequest $primedRequest
     * @param Request       $received
     *
     * @return bool
     *
     * @throws PrimerMethodMismatchException
     * @throws PrimerPathMismatchException
     * @throws PrimerQueryMismatchException
     */
    public function validateRequest(PrimedRequest $primedRequest, Request $received)
    {
        if ($primedRequest->getMethod() !== $received->getMethod()) {
            throw new PrimerMethodMismatchException(sprintf('Expected Method (%s) Received (%s)', $primedRequest->getMethod(), $received->getMethod()));
        }

        if ($primedRequest->getPath() !== $received->getPath()) {
            throw new PrimerPathMismatchException(sprintf('Expected Path (%s) Received (%s)', $primedRequest->getPath(), $received->getPath()));
        }

        if ($primedRequest->getQuery() !== $received->getQuery()) {
            throw new PrimerQueryMismatchException(sprintf('Expected Query (%s) Received (%s)', $primedRequest->getQuery(), $received->getQuery()));
        }

        return true;
    }

    /**
     * Handle Request
     *
     * @param Request  $request  Request
     * @param Response $response Response
     */
    abstract public function handleRequest(Request $request, Response $response);
}
