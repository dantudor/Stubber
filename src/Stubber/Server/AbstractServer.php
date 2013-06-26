<?php

namespace Stubber\Server;

use React\Http\Request;
use React\Http\Response;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\Server as Socket;
use React\Http\Server as HttpServer;
use React\Socket\ConnectionException;

use Stubber\Exception\SocketConnectionException;

/**
 * Class BasicServer
 *
 * @package Stubber\Server
 */
abstract class AbstractServer
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
     * @var \React\EventLoop\LibEventLoop|\React\EventLoop\StreamSelectLoop
     */
    protected $loop;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var HttpServer
     */
    protected $httpServer;

    /**
     * Constructor
     *
     * @param int    $port Port
     * @param string $host Host
     */
    public function __construct($port, $host = '127.0.0.1')
    {
        $this
            ->setPort($port)
            ->setHost($host)
        ;

        $this->loop = EventLoopFactory::create();
        $this->socket = new Socket($this->loop);
        $this->httpServer = new HttpServer($this->socket);

        $server = $this;
        $this->httpServer->on('request', function ($request, $response) use ($server) {
            // @codeCoverageIgnoreStart
            $server->onRequest($request, $response);
            // @codeCoverageIgnoreEnd
        });
    }

    /**
     * Set Port
     *
     * @param int $port
     *
     * @return BasicServer
     */
    public function setPort($port)
    {
        $this->port = (int) $port;

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
     * Set Host
     *
     * @param string $host
     *
     * @return BasicServer
     */
    public function setHost($host)
    {
        $this->host = (string) $host;

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
     * Set Loop
     *
     * @param \React\EventLoop\LibEventLoop|\React\EventLoop\StreamSelectLoop $loop
     *
     * @return BasicServer
     */
    public function setLoop($loop)
    {
        $this->loop = $loop;

        return $this;
    }

    /**
     * Set Socket
     *
     * @param Socket $socket
     *
     * @return BasicServer
     */
    public function setSocket(Socket $socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * Get the Server Socket
     *
     * @return Socket
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Get the Http Server
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
     * @return $this
     * @throws SocketConnectionException
     */
    public function start()
    {
        try {
            $this->socket->listen($this->port, $this->host);
        } catch(ConnectionException $e) {
            throw new SocketConnectionException($e->getMessage(), $e->getCode());
        }

        $this->loop->run();

        return $this;
    }

    /**
     * @param Request  $request  Request
     * @param Response $response Response
     */
    abstract public function onRequest(Request $request, Response $response);
}
