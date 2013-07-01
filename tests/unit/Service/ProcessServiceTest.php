<?php

use Stubber\Service\ProcessService;
use MockFs\MockFs;

/**
 * Class ProcessServiceTest
 */
class ProcessServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MockFs
     */
    protected $mockFs;

    /**
     * @var string
     */
    protected $pidFolder;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->mockFs = new MockFs();
        $this->mockFs->getFileSystem()->addDirectory('StubberTest');

        $this->pidFolder = 'mfs://StubberTest';
    }

    public function tearDown()
    {
        $this->mockFs = null;
    }

    public function testConstructCreatesPidFolder()
    {
        $pidFolder = 'mfs://StubberTest/Fake';

        $this->assertFalse(file_exists($pidFolder));
        new ProcessService($pidFolder);
        $this->assertTrue(file_exists($pidFolder));
    }

    /**
     * @expectedException \Stubber\Exception\ProcessDirectoryException
     */
    public function testConstructThrowsExceptionWhenParentPidFolderIsNotWritable()
    {
        $this->mockFs->getFileSystem()->addDirectory('StubberLocked', '/', 0444);
        $this->mockFs->getFileSystem()->getChildByPath('/StubberLocked')->setOwnerId(100);

        $pidFolder = 'mfs://StubberLocked/Test';

        $this->assertFalse(file_exists($pidFolder));
        new ProcessService($pidFolder);
        $this->assertTrue(file_exists($pidFolder));
    }

    public function testServerExistsReturnsTrue()
    {
        $this->mockFs->getFileSystem()->addFile('99.99.99.99:1234', 9999, '/StubberTest');

        $ps = new ProcessService($this->pidFolder);

        $this->assertTrue($ps->serverExists('99.99.99.99', 1234));
    }

    public function testServerExistsReturnsFalse()
    {
        $ps = new ProcessService($this->pidFolder);

        $this->assertFalse($ps->serverExists('99.99.99.99', 1234));
    }

    public function testAddServer()
    {
        $ps = new ProcessService($this->pidFolder);

        $this->assertFalse($ps->serverExists('99.99.99.99', 1234));
        $this->assertSame($ps, $ps->add('99.99.99.99', 1234, 9999));
        $this->assertTrue($ps->serverExists('99.99.99.99', 1234));
    }

    public function testKillServer()
    {
        $ps = new ProcessService($this->pidFolder);

        $this->assertFalse($ps->serverExists('99.99.99.99', 1234));
        $this->assertSame($ps, $ps->add('99.99.99.99', 1234, 9999));
        $this->assertTrue($ps->serverExists('99.99.99.99', 1234));
        $this->assertSame($ps, $ps->kill('99.99.99.99', 1234));
    }
}