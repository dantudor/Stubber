<?php

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetMethod()
    {
        $defaultMethod = 'GET';
        $method = 'MOCK';
        $request = new \Stubber\Primer\Request();

        $this->assertSame($defaultMethod, $request->getMethod());
        $this->assertSame($request, $request->setMethod($method));
        $this->assertSame($method, $request->getMethod());
    }

    public function testGetSetPath()
    {
        $path = '/mock/path';
        $request = new \Stubber\Primer\Request();

        $this->assertNull($request->getPath());
        $this->assertSame($request, $request->setPath($path));
        $this->assertSame($path, $request->getPath());
    }

    public function testGetSetQuery()
    {
        $defaultQuery = array();
        $query = array('mock' => true);
        $request = new \Stubber\Primer\Request();

        $this->assertSame($defaultQuery, $request->getQuery());
        $this->assertSame($request, $request->setQuery($query));
        $this->assertSame($query, $request->getQuery());
    }

    public function testGetSetHeaders()
    {
        $defaultHeaders = array();
        $headers = array('mock-header' => 'application/mock');
        $request = new \Stubber\Primer\Request();

        $this->assertSame($defaultHeaders, $request->getHeaders());
        $this->assertSame($request, $request->setHeaders($headers));
        $this->assertSame($headers, $request->getHeaders());
    }

    public function testResponseOption()
    {
        $responseOptionName = 'Mock';
        $responseOptionValue = true;
        $defaultResponseOptions = array();
        $responseOptions = array($responseOptionName => $responseOptionValue);

        $request = new \Stubber\Primer\Request();

        $this->assertSame($defaultResponseOptions, $request->getResponseOptions());
        $this->assertSame($request, $request->addResponseOption($responseOptionName, $responseOptionValue));
        $this->assertSame($responseOptionValue, $request->getResponseOption($responseOptionName));
        $this->assertSame($responseOptions, $request->getResponseOptions());
        $this->assertNull($request->getResponseOption('Invalid'));
    }

}
