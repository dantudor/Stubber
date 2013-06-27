<?php

use Stubber\Server;

/**
 * Class ServerTest
 */
class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultLoop()
    {
        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $server = new Server($processService);

        $this->assertInstanceOf('\React\EventLoop\LoopInterface', $server->getLoop());
    }

    public function testSpecificLoop()
    {
        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $loopInterface = Mockery::mock('\React\EventLoop\LibEventLoop');
        $server = new Server($processService, $loopInterface);

        $this->assertSame($loopInterface, $server->getLoop());
    }

    public function testDefaultSocketServer()
    {
        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $server = new Server($processService);

        $this->assertInstanceOf('\React\Socket\Server', $server->getSocketServer());
    }

    public function testSpecificSocketServer()
    {
        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $socketServer = Mockery::mock('\React\Socket\Server');
        $httpServer = Mockery::mock('\React\Http\Server');
        $server = new Server($processService, null, $socketServer, $httpServer);

        $this->assertSame($socketServer, $server->getSocketServer());
    }

    public function testDefaultHttpServer()
    {
        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $server = new Server($processService);

        $this->assertInstanceOf('\React\Http\Server', $server->getHttpServer());
    }

    public function testSpecificHttpServer()
    {
        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $httpServer = Mockery::mock('\React\Http\Server');
        $server = new Server($processService, null, null, $httpServer);

        $this->assertSame($httpServer, $server->getHttpServer());
    }

    /**
     * @expectedException \Stubber\Exception\SocketConnectionException
     */
    public function testStartServerConnectionError()
    {
        $host = '127.0.0.1';
        $port = 8080;

        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $loopInterface = Mockery::mock('\React\EventLoop\LibEventLoop');
        $socketServer = Mockery::mock('\React\Socket\Server');
        $httpServer = Mockery::mock('\React\Http\Server');

        $processService->shouldReceive('kill')->twice()->with($host, $port);
        $processService->shouldReceive('fork')->once();
        $socketServer->shouldReceive('listen')->andThrow('\React\Socket\ConnectionException');

        $server = new Server($processService, $loopInterface, $socketServer, $httpServer);
        $this->assertSame($server, $server->start($host, $port));
    }

    public function testStartServerSucess()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $posixId = 123456;

        $processService = Mockery::mock('\Stubber\Service\ProcessService');
        $loopInterface = Mockery::mock('\React\EventLoop\LibEventLoop');
        $socketServer = Mockery::mock('\React\Socket\Server');
        $httpServer = Mockery::mock('\React\Http\Server');

        $processService->shouldReceive('kill')->twice()->with($host, $port);
        $processService->shouldReceive('fork')->once()->andReturn($posixId);
        $socketServer->shouldReceive('listen')->once()->with($port, $host);
        $processService->shouldReceive('add')->once()->with($host, $port, $posixId);
        $loopInterface->shouldReceive('run')->once();

        $server = new Server($processService, $loopInterface, $socketServer, $httpServer);
        $this->assertSame($server, $server->start($host, $port));
    }
}