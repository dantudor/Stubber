<?php

use Stubber\Server\AbstractServer;
use React\Http\Request;
use React\Http\Response;

class AbstractServerTest extends PHPUnit_Framework_TestCase
{
    public function testServerInterfaceGetPort()
    {
        $port = 8080;
        $server = new TestServer($port);

        $this->assertSame($port, $server->getPort());
        $this->assertSame($server, $server->setPort($port));
        $this->assertSame($port, $server->getPort());
    }

    public function testServerInterfaceGetHost()
    {
        $host = 'mock.host';
        $server = new TestServer(0, $host);

        $this->assertSame($host, $server->getHost());
        $this->assertSame($server, $server->setHost($host));
        $this->assertSame($host, $server->getHost());
    }

    public function testServerInterfaceGetSocket()
    {
        $socketType = '\React\Socket\Server';
        $server = new TestServer(8080);

        $this->assertInstanceOf($socketType, $server->getSocket());
    }

    public function testServerInterfaceGetHttpServer()
    {
        $serverType = '\React\Http\Server';
        $server = new TestServer(8080);

        $this->assertInstanceOf($serverType, $server->getHttpServer());
    }

    /**
     * @expectedException \Stubber\Exception\SocketConnectionException
     */
    public function testServerInterfaceStartThrowsExceptionWhenBindingFails()
    {
        $server = new TestServer(8080);

        // Stud the Event Loop
        $loop = $this->getMockBuilder('\React\EventLoop\LibEventLoop')->disableOriginalConstructor()->getMock();
        $server->setLoop($loop);

        // Stub the socket
        $socket = $this->getMockBuilder('\React\Socket\Server')->disableOriginalConstructor()->getMock();
        $server->setSocket($socket);

        $socket
            ->expects($this->once())
            ->method('listen')
            ->will($this->throwException(new \React\Socket\ConnectionException));

        $server->start();
    }

    public function testServerInterfaceStartSuccess()
    {
        $server = new TestServer(8080);

        // Stud the Event Loop
        $loop = $this->getMockBuilder('\React\EventLoop\LibEventLoop')->disableOriginalConstructor()->getMock();
        $server->setLoop($loop);

        // Stub the socket
        $socket = $this->getMockBuilder('\React\Socket\Server')->disableOriginalConstructor()->getMock();
        $server->setSocket($socket);

        $socket
            ->expects($this->once())
            ->method('listen')
            ->will($this->returnValue(true));

        $server->start();
    }
}

class TestServer extends AbstractServer
{
    public function onRequest(Request $request, Response $response)
    {
        // Intentioanlly empty
    }
}