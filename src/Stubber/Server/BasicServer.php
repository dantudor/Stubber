<?php

namespace Stubber\Server;

use React\Http\Request;
use React\Http\Response;

/**
 * Class BasicServer
 *
 * @package Stubber\Server
 */
class BasicServer extends AbstractServer
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        $response->writeHead(200, array('Content-Type' => 'text/html'));
        $response->end('Stubber Documentation');
    }
}