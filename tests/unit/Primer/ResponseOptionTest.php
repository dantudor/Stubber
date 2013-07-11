<?php


class ResponseOptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $name = 'MockName';
        $value = 'MockValue';

        $responseOption = new \Stubber\Primer\ResponseOption($name, $value);

        $this->assertSame($name, $responseOption->getName());
    }

    public function testGetValue()
    {
        $name = 'MockName';
        $value = 'MockValue';

        $responseOption = new \Stubber\Primer\ResponseOption($name, $value);

        $this->assertSame($value, $responseOption->getValue());
    }
}
