<?php

namespace Stubber\Primer;

/**
 * Class ResponseOption
 *
 * @package Stubber\Primer
 */
class ResponseOption
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     *
     * @param string $name
     * @param mixed $value
     */
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}