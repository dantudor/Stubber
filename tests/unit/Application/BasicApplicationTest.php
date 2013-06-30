<?php

use Stubber\Application\BasicApplication;

class BasicApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testHandleRequest()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $server = Mockery::mock('\Stubber\Server');
        $request = Mockery::mock('\React\Http\Request');

        $response = Mockery::mock('\React\Http\Response');
        $response->shouldReceive('writeHead')->once()->with(200, array('Content-Type' => 'text/html'));
        $response->shouldReceive('end')->once()->with('Stubber Documentation');

        $application = new BasicApplication($server);
        $application->setHost($host)->setPort($port);
        $application->handleRequest($request, $response);
    }
}