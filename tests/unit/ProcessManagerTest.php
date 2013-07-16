<?php

class ProcessManagerTest extends PHPUnit_Framework_TestCase
{

    public function testPidExistsSuccess()
    {
        $filesystem = Mockery::mock('\Symfony\Component\Filesystem\Filesystem');
        $filesystem->shouldReceive('exists')->andReturn(true);

        $processControlService = Mockery::mock('\ProcessControl\ProcessControlService');

        $mockFs = new \MockFs\MockFs();
        $mockFs->getFileSystem()->addDirectory('process');

        $processManager = new \Stubber\ProcessManager($filesystem, $processControlService, 'mfs::/process');

        $this->assertTrue($processManager->pidExists('127.0.0.1', 8080));
    }

    public function testPidExistsFailure()
    {
        $filesystem = Mockery::mock('\Symfony\Component\Filesystem\Filesystem');
        $filesystem->shouldReceive('exists')->andReturn(false);
        $filesystem->shouldReceive('mkdir')->andReturn(true);

        $processControlService = Mockery::mock('\ProcessControl\ProcessControlService');

        new \MockFs\MockFs();

        $processManager = new \Stubber\ProcessManager($filesystem, $processControlService, 'mfs://process');

        $this->assertFalse($processManager->pidExists('127.0.0.1', 8080));
    }

    public function testRegisterNewPid()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $pid = '1234';

        $filesystem = Mockery::mock('\Symfony\Component\Filesystem\Filesystem');
        $filesystem->shouldReceive('exists')->andReturn(false);
        $filesystem->shouldReceive('mkdir')->andReturn(true);

        $processControlService = Mockery::mock('\ProcessControl\ProcessControlService');

        $mockFs = new \MockFs\MockFs();
        $mockFs->getFileSystem()->addDirectory('process');

        $processManager = new \Stubber\ProcessManager($filesystem, $processControlService, 'mfs://process');

        $this->assertSame($processManager, $processManager->registerPid($host, $port, $pid));
        $this->assertTrue(file_exists('mfs://process/' . $host . '-' . $port));
        $this->assertSame($pid, file_get_contents('mfs://process/' . $host . '-' . $port));
    }

    public function testRegisterReplacementPid()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $oldPid = '1234';
        $pid = '5678';

        $filesystem = Mockery::mock('\Symfony\Component\Filesystem\Filesystem');
        $filesystem->shouldReceive('exists')->andReturn(true);
        $filesystem->shouldReceive('remove')->andReturn(true);

        $childProcess = Mockery::mock('\ProcessControl\Process');

        $masterProcess = Mockery::mock('\ProcessControl\Process');
        $masterProcess->shouldReceive('hasChildById')->with($oldPid)->andReturn(true);
        $masterProcess->shouldReceive('getChildById')->with($oldPid)->andReturn($childProcess);

        $processControlService = Mockery::mock('\ProcessControl\ProcessControlService');
        $processControlService->shouldReceive('getMaster')->andReturn($masterProcess);
        $processControlService->shouldReceive('terminateProcess')->with($childProcess)->andReturn(true);

        $mockFs = new \MockFs\MockFs();
        $mockFs->getFileSystem()->addFile('127.0.0.1-8080', $oldPid, '/process');

        $processManager = new \Stubber\ProcessManager($filesystem, $processControlService, 'mfs://process');

        $this->assertTrue(file_exists('mfs://process/' . $host . '-' . $port));
        $this->assertSame($oldPid, file_get_contents('mfs://process/' . $host . '-' . $port));
        $this->assertSame($processManager, $processManager->registerPid($host, $port, $pid));
        $this->assertTrue(file_exists('mfs://process/' . $host . '-' . $port));
        $this->assertSame($pid, file_get_contents('mfs://process/' . $host . '-' . $port));
    }
}
