<?php

namespace Stubber\Service;

use Stubber\Server\BasicServer;

/**
 * Class ServerService
 *
 * @package Stubber\Service
 */
class ServerService
{
    /**
     * @var ProcessService
     */
    protected $processService;

    /**
     * Constructor
     *
     * @param ProcessService $processService
     */
    public function __construct(ProcessService $processService)
    {
        $this->processService = $processService;
    }

    /**
     * Create
     *
     * @param int    $port
     * @param string $host
     * 
     * @return BasicServer
     */
    public function create($port, $host = '127.0.0.1')
    {
        $this->processService->kill($host, $port);

        /** @var $server BasicServer */
        $server = new BasicServer($port, $host);

        try {
            $posixId = $this->processService->fork();
            $this->processService->add($host, $port, $posixId);

            $server->start();
        } catch (SocketConnectionException $e) {
            $this->processService->kill($host, $port);
        }

        return $server;
    }

}