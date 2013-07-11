<?php

namespace Stubber\Primer;

use JMS\Serializer\Annotation\Type;

/**
 * Class Request
 *
 * @package Stubber\Primer
 */
class Request
{
    /**
     * @var string
     * @Type("string")
     */
    protected $method = 'GET';

    /**
     * @var string
     * @Type("string")
     */
    protected $path;

    /**
     * @var string
     * @Type("array")
     */
    protected $query = array();

    /**
     * @var array
     * @Type("array")
     */
    protected $headers = array();

    /**
     * @var array|ResponseOption
     * @Type("array")
     */
    protected $responseOptions = array();

    /**
     * Set Method
     *
     * @param string $method
     *
     * @return Request
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set Path
     *
     * @param string $path
     *
     * @return Request
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set Query
     *
     * @param string $query
     *
     * @return Request
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get Query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set Headers
     *
     * @param array $headers
     *
     * @return Request
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get Headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Add ResponseOptions
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Request
     */
    public function addResponseOption($name, $value)
    {
        $this->responseOptions[$name] = $value;

        return $this;
    }

    /**
     * Get Response Options
     *
     * @return array|ResponseOption
     */
    public function getResponseOptions()
    {
        return $this->responseOptions;
    }

    /**
     * Get Response Option
     *
     * @param string $name
     *
     * @return null|mixed
     */
    public function getResponseOption($name)
    {
        if (array_key_exists($name, $this->responseOptions)) {
            return $this->responseOptions[$name];
        }

        return null;
    }
}