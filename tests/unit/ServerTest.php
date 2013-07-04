<?php

use Stubber\Server;

/**
 * Class ServerTest
 */
class ServerTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->mockProcessService = Mockery::mock('\Pagon\ChildProcess\ChildProcess');
    }

    public function testGetSetHost()
    {
        $host = '127.0.0.1';
        $server = new Server($this->mockProcessService);

        $this->assertNull($server->getHost());
        $this->assertSame($server, $server->setHost($host));
        $this->assertSame($host, $server->getHost());
    }

    public function testGetSetPort()
    {
        $port = 8080;
        $server = new Server($this->mockProcessService);

        $this->assertNull($server->getPort());
        $this->assertSame($server, $server->setPort($port));
        $this->assertSame($port, $server->getPort());
    }

    public function testDefaultLoop()
    {
        $server = new Server($this->mockProcessService);

        $this->assertInstanceOf('\React\EventLoop\LoopInterface', $server->getLoop());
    }

    public function testSpecificLoop()
    {
        $loopInterface = Mockery::mock('\React\EventLoop\LibEventLoop');
        $server = new Server($this->mockProcessService, $loopInterface);

        $this->assertSame($loopInterface, $server->getLoop());
    }

    public function testDefaultSocketServer()
    {
        $server = new Server($this->mockProcessService);

        $this->assertInstanceOf('\React\Socket\Server', $server->getSocketServer());
    }

    public function testSpecificSocketServer()
    {
        $socketServer = Mockery::mock('\React\Socket\Server');
        $httpServer = Mockery::mock('\React\Http\Server');
        $server = new Server($this->mockProcessService, null, $socketServer, $httpServer);

        $this->assertSame($socketServer, $server->getSocketServer());
    }

    public function testDefaultHttpServer()
    {
        $server = new Server($this->mockProcessService);

        $this->assertInstanceOf('\React\Http\Server', $server->getHttpServer());
    }

    public function testSpecificHttpServer()
    {
        $httpServer = Mockery::mock('\React\Http\Server');
        $server = new Server($this->mockProcessService, null, null, $httpServer);

        $this->assertSame($httpServer, $server->getHttpServer());
    }

    public function testDefaultPrimer()
    {
        $server = new Server($this->mockProcessService);

        $this->assertInstanceOf('\Stubber\Primer', $server->getPrimer());
    }

    public function testSpecificPrimer()
    {
        $primer = Mockery::mock('\Stubber\Primer');
        $primer->shouldReceive('setServer')->once()->andReturn($primer);
        $server = new Server($this->mockProcessService, null, null, null, $primer);

        $this->assertSame($primer, $server->getPrimer());
    }

    public function testStart()
    {
        $mockProcess = Mockery::mock('\Pagon\ChildProcess\Process');
        $this->mockProcessService->shouldReceive('parallel')->once()->andReturn($mockProcess);

        $server = new Server($this->mockProcessService);

        $this->assertSame($server, $server->start());
        $this->assertSame($mockProcess, $server->getPrimer()->getProcess());
    }
}