<?php

use Stubber\Server;

/**
 * Class ServerTest
 */
class ServerTest extends PHPUnit_Framework_TestCase
{
    protected $mockProcessManager;

    protected $mockPrimer;

    public function setup()
    {
        $this->mockProcessManager = Mockery::mock('\Stubber\ProcessManager');
        $this->mockProcessManager->shouldReceive('wait');

        $this->mockPrimer = Mockery::mock('\Stubber\Primer');
        $this->mockPrimer->shouldReceive('setServer')->andReturn($this->mockPrimer);
    }

    public function testGetPrimer()
    {
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertSame($this->mockPrimer, $server->getPrimer());
    }

    public function testGetDefaultProcess()
    {
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertNull($server->getProcess());
    }

    public function testGetSetApplication()
    {
        $application = Mockery::mock('\Stubber\Application\BasicApplication');
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertNull($server->getApplication());
        $this->assertSame($server, $server->setApplication($application));
        $this->assertSame($application, $server->getApplication());
    }

    public function testGetSetHost()
    {
        $host = '127.0.0.1';
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertNull($server->getHost());
        $this->assertSame($server, $server->setHost($host));
        $this->assertSame($host, $server->getHost());
    }

    public function testGetSetPort()
    {
        $port = 8080;
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertNull($server->getPort());
        $this->assertSame($server, $server->setPort($port));
        $this->assertSame($port, $server->getPort());
    }

    public function testDefaultLoop()
    {
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertInstanceOf('\React\EventLoop\LoopInterface', $server->getLoop());
    }

    public function testSpecificLoop()
    {
        $loopInterface = Mockery::mock('\React\EventLoop\LibEventLoop');
        $server = new Server($this->mockProcessManager, $this->mockPrimer, $loopInterface);

        $this->assertSame($loopInterface, $server->getLoop());
    }

    public function testDefaultSocketServer()
    {
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertInstanceOf('\React\Socket\Server', $server->getSocketServer());
    }

    public function testSpecificSocketServer()
    {
        $socketServer = Mockery::mock('\React\Socket\Server');
        $httpServer = Mockery::mock('\React\Http\Server');
        $server = new Server($this->mockProcessManager, $this->mockPrimer, null, $socketServer, $httpServer);

        $this->assertSame($socketServer, $server->getSocketServer());
    }

    public function testDefaultHttpServer()
    {
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertInstanceOf('\React\Http\Server', $server->getHttpServer());
    }

    public function testSpecificHttpServer()
    {
        $httpServer = Mockery::mock('\React\Http\Server');
        $server = new Server($this->mockProcessManager, $this->mockPrimer, null, null, $httpServer);

        $this->assertSame($httpServer, $server->getHttpServer());
    }

    public function testGetProcessManager()
    {
        $processManager = $this->mockProcessManager;
        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $this->assertSame($processManager, $server->getProcessManager());
    }

    public function testStart()
    {
        $mockProcess = Mockery::mock('Pagon\ChildProcess\Process');
        $this->mockPrimer->shouldReceive('prepare')->andReturn($this->mockPrimer);
        $this->mockProcessManager->shouldReceive('parallel')->andReturn($mockProcess);

        $server = new Server($this->mockProcessManager, $this->mockPrimer);

        $server->start();
    }
}