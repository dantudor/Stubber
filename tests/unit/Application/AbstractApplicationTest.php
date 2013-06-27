<?php

use Mockery as m;
use Stubber\Application\AbstractApplication;
use React\Http\Request;
use React\Http\Response;

class AbstractApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testGetHost()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');

        $application = new TestApplication($host, $port, $server);

        $this->assertSame($host, $application->getHost());
    }

    public function testGetPort()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');

        $application = new TestApplication($host, $port, $server);

        $this->assertSame($port, $application->getPort());
    }

    public function testGetServer()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');

        $application = new TestApplication($host, $port, $server);

        $this->assertSame($server, $application->getServer());
    }

    public function testRun()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');
        $httpServer = Mockery::mock('\React\Http\Server');
        $httpServer->shouldReceive('on')->once();
        $server->shouldReceive('getHttpServer')->once()->andReturn($httpServer);
        $server->shouldReceive('start')->once();

        $application = new TestApplication($host, $port, $server);
        $application->run();
    }
}

/**
 * Class TestApplication
 */
class TestApplication extends AbstractApplication
{
    public function handleRequest(Request $request, Response $response)
    {

    }
}