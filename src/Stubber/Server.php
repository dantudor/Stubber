<?php

namespace Stubber;

use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use React\Socket\ConnectionException;
use Stubber\Service\ProcessService;
use Stubber\Exception\SocketConnectionException;


/**
 * Class Server
 *
 * @package Stubber
 */
class Server
{
    /**
     * @var ProcessService
     */
    protected $processService;

    /**
     * @var \React\EventLoop\LibEventLoop|\React\EventLoop\StreamSelectLoop
     */
    protected $loop;

    /**
     * @var \React\Socket\Server
     */
    protected $socketServer;

    /**
     * @var \React\Http\Server
     */
    protected $httpServer;

    /**
     * Constructor
     *
     * @param ProcessService $processService
     * @param LoopInterface  $loop
     * @param SocketServer   $socketServer
     * @param HttpServer     $httpServer
     */
    public function __construct(ProcessService $processService, LoopInterface $loop = null, SocketServer $socketServer = null, HttpServer $httpServer = null)
    {
        $this->processService = $processService;

        if (is_null($loop)) {
            $this->loop = EventLoopFactory::create();
        } else {
            $this->loop = $loop;
        }

        if (is_null($socketServer)) {
            $this->socketServer = new SocketServer($this->loop);
        } else {
            $this->socketServer = $socketServer;
        }

        if (is_null($httpServer)) {
            $this->httpServer = new HttpServer($this->socketServer);
        } else {
            $this->httpServer = $httpServer;
        }
    }

    /**
     * Get loop
     *
     * @return \React\EventLoop\LibEventLoop|\React\EventLoop\StreamSelectLoop
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Get Socket Server
     *
     * @return SocketServer
     */
    public function getSocketServer()
    {
        return $this->socketServer;
    }

    /**
     * Get http server
     *
     * @return HttpServer
     */
    public function getHttpServer()
    {
        return $this->httpServer;
    }

    /**
     * Start the server
     *
     * @param string $host
     * @param int    $port
     *
     * @return Server
     * @throws SocketConnectionException
     */
    public function start($host, $port)
    {
        $this->processService->kill($host, $port);

        $posixId = $this->processService->fork();

        try {
            $this->socketServer->listen($port, $host);
            $this->processService->add($host, $port, $posixId);
        } catch(ConnectionException $e) {
            $this->processService->kill($host, $port);
            throw new SocketConnectionException($e->getMessage(), $e->getCode());
        }

        $this->loop->run();

        return $this;
    }
}