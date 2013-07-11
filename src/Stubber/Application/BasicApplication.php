<?php

namespace Stubber\Application;

use React\Http\Request;
use React\Http\Response;
use Stubber\Exception\PrimerException;

/**
 * Class BasicApplication
 *
 * @package Stubber\Application
 */
class BasicApplication extends AbstractApplication
{
    public function handleRequest(Request $request, Response $response)
    {
        try {
            $expectedRequest = $this->getExpectedRequest();
            if (true === $this->validateRequest($expectedRequest, $request)) {
                $response->writeHead($expectedRequest->getResponseOption('status'), array('Content-Type' => 'text/html'));
                $response->end('Stubber Basic Application');
            }
        } catch(PrimerException $e) {
            $response->writeHead(418, array('Content-Type' => 'text/html'));
            $response->end('Stubber not primed for this request');
        }
    }
}