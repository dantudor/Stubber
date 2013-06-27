<?php

namespace Stubber;

use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use React\Socket\ConnectionException;
use Stubber\Service\ProcessService;

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
     */
    public function __construct(ProcessService $processService)
    {
        $this->processService = $processService;

        $this->loop = EventLoopFactory::create();
        $this->socketServer = new SocketServer($this->loop);
        $this->httpServer = new HttpServer($this->socketServer);
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