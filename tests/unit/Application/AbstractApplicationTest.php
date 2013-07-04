<?php

use Mockery as m;
use Stubber\Application\AbstractApplication;
use React\Http\Request;
use React\Http\Response;

class AbstractApplicationTest extends PHPUnit_Framework_TestCase
{
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
        $server->shouldReceive('setHost')->once()->andReturn($server);
        $server->shouldReceive('setPort')->once()->andReturn($server);;
        $httpServer = Mockery::mock('\React\Http\Server');
        $httpServer->shouldReceive('on')->once();
        $server->shouldReceive('getHttpServer')->once()->andReturn($httpServer);
        $server->shouldReceive('start')->once();

        $application = new TestApplication($server);
        $application->getServer()
            ->setHost($host)
            ->setPort($port)
        ;

        $application->run();
    }

    public function testSetServerHost()
    {
        $host = '127.0.0.1';
        $server = Mockery::mock('\Stubber\Server');
        $application = new TestApplication($server);

        $this->assertSame($application, $application->setServerHost($host));
        $this->assertSame($host, $application->getServerHost());
    }

    public function testSetServerPort()
    {
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');
        $application = new TestApplication($server);

        $this->assertSame($application, $application->setServerPort($port));
        $this->assertSame($port, $application->getServerPort());
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

    public function getServerHost()
    {
        return $this->serverHost;
    }

    public function getServerPort()
    {
        return $this->serverPort;
    }
}