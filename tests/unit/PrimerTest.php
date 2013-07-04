<?php

use Stubber\Primer;

class PrimerTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetServer()
    {
        $server = Mockery::mock('\Stubber\Server');
        $primer = new Primer();

        $this->assertNull($primer->getServer());
        $this->assertSame($primer, $primer->setServer($server));
        $this->assertSame($server, $primer->getServer());
    }

    public function testGetSetProcess()
    {
        $process = Mockery::mock('\Pagon\ChildProcess\Process');
        $primer = new Primer();

        $this->assertNull($primer->getServer());
        $this->assertSame($primer, $primer->setProcess($process));
        $this->assertSame($process, $primer->getProcess());
    }
}
