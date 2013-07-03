<?php

namespace Stubber;

use Pagon\ChildProcess\ChildProcess;
use Pagon\ChildProcess\Process;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use React\Socket\ConnectionException;

/**
 * Class Server
 *
 * @package Stubber
 */
class Server
{
    /**
     * @var ChildProcess
     */
    protected $process;

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
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * Constructor
     *
     * @param LoopInterface $loop
     * @param SocketServer $socketServer
     * @param HttpServer $httpServer
     */
    public function __construct(LoopInterface $loop = null, SocketServer $socketServer = null, HttpServer $httpServer = null)
    {
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
     * Set Host
     *
     * @param string $host
     *
     * @return Server
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
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
     * Set Port
     *
     * @param int $port
     *
     * @return Server
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
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
     * Start the server
     *
     * @return Server
     * @throws SocketConnectionException
     */
    public function start()
    {
        $server = $this;

        $this->process = new ChildProcess();
        $this->process->parallel(function (Process $process) use ($server) {
            try {
                $server->getSocketServer()->listen($server->getPort(), $server->getHost());
            } catch(ConnectionException $e) {
                $process->kill(9);
            }

            $server->getLoop()->run();
        });

        return $this;
    }
}
