<?php

namespace Stubber\Application;

use React\Http\Request;
use React\Http\Response;

/**
 * Class BasicApplication
 *
 * @package Stubber\Application
 */
class BasicApplication extends AbstractApplication
{
    /**
     * handleRequest
     *
     * @param Request  $request
     * @param Response $response
     */
    public function handleRequest(Request $request, Response $response)
    {
        $response->writeHead(200, array('Content-Type' => 'text/html'));
        $response->end('Stubber Documentation');
    }
}