<?php

use Mockery as m;
use Stubber\Application\AbstractApplication;
use Stubber\Http\Request;
use Stubber\Http\Response;

class AbstractApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testGetServer()
    {
        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);

        $this->assertSame($server, $application->getServer());
    }

    public function testRun()
    {
        $host = '127.0.0.1';
        $port = 8080;

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);
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
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);

        $this->assertSame($application, $application->setServerHost($host));
        $this->assertSame($host, $application->getServerHost());
    }

    public function testSetServerPort()
    {
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);

        $this->assertSame($application, $application->setServerPort($port));
        $this->assertSame($port, $application->getServerPort());
    }

    /**
     * @expectedException Stubber\Exception\PrimerMissingException
     */
    public function testGetExpectedRequestThrowsException()
    {
        $primer = Mockery::mock('\Stubber\Primer');
        $primer->shouldReceive('isPrimed')->andReturn(false);

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);
        $server->shouldReceive('getPrimer')->andReturn($primer);

        $application = new TestApplication($server);
        $application->getExpectedRequest();
    }

    public function testGetExpectedRequestSuccess()
    {
        $primer = Mockery::mock('\Stubber\Primer');
        $primer->shouldReceive('isPrimed')->andReturn(true);
        $primer->shouldReceive('getNextPrimedRequest');

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);
        $server->shouldReceive('getPrimer')->andReturn($primer);

        $application = new TestApplication($server);
        $application->getExpectedRequest();
    }

    /**
     * @expectedException \Stubber\Exception\PrimerMethodMismatchException
     */
    public function testValidateRequestThrowsMethodMismatchException()
    {
        $primedRequest = Mockery::mock('\Stubber\Primer\Request');
        $primedRequest->shouldReceive('getMethod')->andReturn(1);

        $request = Mockery::mock('\React\Http\Request');
        $request->shouldReceive('getMethod')->andReturn(2);

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);
        $application->validateRequest($primedRequest, $request);
    }

    /**
     * @expectedException \Stubber\Exception\PrimerPathMismatchException
     */
    public function testValidateRequestThrowsPathMismatchException()
    {
        $primedRequest = Mockery::mock('\Stubber\Primer\Request');
        $primedRequest->shouldReceive('getMethod')->andReturn(0);
        $primedRequest->shouldReceive('getPath')->andReturn(1);

        $request = Mockery::mock('\React\Http\Request');
        $request->shouldReceive('getMethod')->andReturn(0);
        $request->shouldReceive('getPath')->andReturn(2);

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);
        $application->validateRequest($primedRequest, $request);
    }

    /**
     * @expectedException \Stubber\Exception\PrimerQueryMismatchException
     */
    public function testValidateRequestThrowsQueryMismatchException()
    {
        $primedRequest = Mockery::mock('\Stubber\Primer\Request');
        $primedRequest->shouldReceive('getMethod')->andReturn(0);
        $primedRequest->shouldReceive('getPath')->andReturn(0);
        $primedRequest->shouldReceive('getQuery')->andReturn(1);

        $request = Mockery::mock('\React\Http\Request');
        $request->shouldReceive('getMethod')->andReturn(0);
        $request->shouldReceive('getPath')->andReturn(0);
        $request->shouldReceive('getQuery')->andReturn(2);

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);
        $application->validateRequest($primedRequest, $request);
    }

    public function testValidateRequestSuccess()
    {
        $primedRequest = Mockery::mock('\Stubber\Primer\Request');
        $primedRequest->shouldReceive('getMethod')->andReturn(0);
        $primedRequest->shouldReceive('getPath')->andReturn(0);
        $primedRequest->shouldReceive('getQuery')->andReturn(0);

        $request = Mockery::mock('\React\Http\Request');
        $request->shouldReceive('getMethod')->andReturn(0);
        $request->shouldReceive('getPath')->andReturn(0);
        $request->shouldReceive('getQuery')->andReturn(0);

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('setApplication')->andReturn($server);

        $application = new TestApplication($server);
        $application->validateRequest($primedRequest, $request);
    }
}

/**
 * Class TestApplication
 */
class TestApplication extends AbstractApplication
{
    public function handleRequest(React\Http\Request $request, React\Http\Response $response)
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