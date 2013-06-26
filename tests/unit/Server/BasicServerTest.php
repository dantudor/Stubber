<?php

use Stubber\Server\BasicServer;


class BasicServerTest extends PHPUnit_Framework_TestCase
{
    public function testOnRequest()
    {
        $mockRequest = $this->getMockBuilder('\React\Http\Request')->disableOriginalConstructor()->getMock();

        $MockResponse = $this->getMockBuilder('\React\Http\Response')->disableOriginalConstructor()->getMock();
        $MockResponse->expects($this->once())->method('writeHead');
        $MockResponse->expects($this->once())->method('end');

        $bs = new BasicServer(8080);
        $bs->onRequest($mockRequest, $MockResponse);
    }
}