<?php

namespace Stubber;

use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use React\Socket\ConnectionException;
use Pagon\ChildProcess\Process;
use Stubber\Application\AbstractApplication;

/**
 * Class Server
 *
 * @package Stubber
 */
class Server
{
    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * @var Primer
     */
    protected $primer;

    /**
     * @var Process
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
     * @var AbstractApplication
     */
    protected $application;

    /**
     * Constructor
     *
     * @param ProcessManager $processManager
     * @param Primer         $primer
     * @param LoopInterface  $loop
     * @param SocketServer   $socketServer
     * @param HttpServer     $httpServer
     */
    public function __construct(ProcessManager $processManager, Primer $primer, LoopInterface $loop = null, SocketServer $socketServer = null, HttpServer $httpServer = null)
    {
        $this->processManager = $processManager;

        $this->primer = $primer;
        $this->primer->setServer($this);

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
     * Get Process Manager
     *
     * @return ProcessManager
     */
    public function getProcessManager()
    {
        return $this->processManager;
    }

    /**
     * Get Prime Manager
     *
     * @return Primer
     */
    public function getPrimer()
    {
        return $this->primer;
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
     * Get Process
     *
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set Application
     *
     * @param AbstractApplication $application
     *
     * @return $this
     */
    public function setApplication(AbstractApplication $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get Application
     *
     * @return AbstractApplication
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Start the server
     *
     * @return Server
     * @throws SocketConnectionException
     */
    public function start()
    {
        $this->primer->prepare();

        $server = $this;
        $processManager = $this->processManager;

        $this->process = $this->processManager->parallel(function(\ProcessControl\Process $process) use ($server, $processManager) {
            try {
                $processManager->registerProcess($process, $server->getHost(), $server->getPort());
                $server->getSocketServer()->listen($server->getPort(), $server->getHost());
            } catch(ConnectionException $e) {
                $processManager->terminateProcess($process);
            }

            $server->getLoop()->run();
        });

        return $this;
    }
}
