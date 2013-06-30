<?php

use Mockery as m;
use Stubber\Application\AbstractApplication;
use React\Http\Request;
use React\Http\Response;

class AbstractApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetHost()
    {
        $host = '127.0.0.1';
        $server = Mockery::mock('\Stubber\Server');

        $application = new TestApplication($server);

        $this->assertNull($application->getHost());
        $this->assertSame($application, $application->setHost($host));
        $this->assertSame($host, $application->getHost());
    }

    public function testGetPort()
    {
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');

        $application = new TestApplication($server);

        $this->assertNull($application->getPort());
        $this->assertSame($application, $application->setPort($port));
        $this->assertSame($port, $application->getPort());
    }

    public function testGetServer()
    {
        $server = Mockery::mock('\Stubber\Server');

        $application = new TestApplication($server);

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

        $application = new TestApplication($server);
        $application->setHost($host)->setPort($port);
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