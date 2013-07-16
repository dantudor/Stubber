<?php

use Stubber\ProcessManager;
use MockFs\MockFs;
use ProcessControl\Process;

class ProcessManagerTest extends PHPUnit_Framework_TestCase
{
    protected $mockFilesystem;

    protected $mockFinder;

    /**
     * @var MockFs
     */
    protected $mockFs;

    public function setUp()
    {
        $this->mockFilesystem = Mockery::mock('\Symfony\Component\Filesystem\Filesystem');
        $this->mockFinder = new Symfony\Component\Finder\Finder();
        $this->mockFs = new MockFs();
    }

    public function testConstructMissingDirectory()
    {
        $this->mockFs->getFileSystem()->addDirectory('stubber');
        $pidFolder = 'mfs://stubber/process';

        $this->mockFilesystem->shouldReceive('exists')->with($pidFolder)->andReturn(false);
        $this->mockFilesystem->shouldReceive('mkdir')->with($pidFolder, 0777, true)->andReturn(true);

        new ProcessManager(new \Symfony\Component\Filesystem\Filesystem(), $this->mockFinder, $pidFolder);

        $this->assertInstanceOf('\MockFs\Object\Directory', $this->mockFs->getFileSystem()->getChildByPath('/stubber/process'));
    }

    public function testConstructWithDirectory()
    {
        $pidFolder = 'mfs://stubber/process';
        $this->mockFs->getFileSystem()->addDirectory('process', '/stubber');

        $this->mockFilesystem->shouldReceive('exists')->with($pidFolder)->andReturn(true);

        new ProcessManager($this->mockFilesystem, $this->mockFinder, $pidFolder);
    }

    public function testHydrateFromFileWithTwoResults()
    {
        $pidFolder = 'mfs://stubber/process';
        $this->mockFs->getFileSystem()->addDirectory('process', '/stubber');
        $this->mockFs->getFileSystem()->addFile('127.0.0.1:8080', 1234, '/stubber/process');
        $this->mockFs->getFileSystem()->addFile('127.0.0.1:8888', 5678, '/stubber/process');

        $this->mockFilesystem->shouldReceive('exists')->with($pidFolder)->andReturn(true);

        $processManager = new ProcessManager($this->mockFilesystem, $this->mockFinder, $pidFolder);
        $this->assertSame(
            count($this->mockFs->getFileSystem()->getChildByPath('/stubber/process')->getChildren()),
            $processManager->getMaster()->getChildCount()
        );
    }

    public function testRegisterProcessTerminatesExisting()
    {
        $pidFolder = 'mfs://stubber/process';
        $this->mockFs->getFileSystem()->addDirectory('process', '/stubber');
        $this->mockFs->getFileSystem()->addFile('127.0.0.1:8080', 1234, '/stubber/process');

        $this->mockFilesystem->shouldReceive('exists')->with($pidFolder)->andReturn(true);
        $this->mockFilesystem->shouldReceive('remove')->with($pidFolder . '/127.0.0.1:8080')->andReturn(true);

        $processManager = new ProcessManager($this->mockFilesystem, $this->mockFinder, $pidFolder);
        $processManager->registerProcess(
            new Process(1234),
            '127.0.0.1',
            'mock'
        );



        $this->assertSame('1234', file_get_contents($pidFolder . '/127.0.0.1:mock'));
    }
}
