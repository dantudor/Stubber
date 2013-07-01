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