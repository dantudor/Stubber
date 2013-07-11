<?php

use Stubber\Primer;
use Stubber\Primer\Request as PrimedRequest;
use MockFs\MockFs;
use Symfony\Component\Filesystem\Filesystem;

class PrimerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    protected $mockFilesystem;

    /**
     * @var JMS\Serializer\Serializer
     */
    protected $mockSerializer;

    public function setUp()
    {
        $this->mockFilesystem = Mockery::mock('Symfony\Component\Filesystem\Filesystem');

        $this->mockSerializer = Mockery::mock('JMS\Serializer\Serializer');
    }

    public function testGetSetServer()
    {
        $this->mockFilesystem->shouldReceive('exists')->andReturn(true);

        $primer = new Primer($this->mockFilesystem, $this->mockSerializer);
        $server = Mockery::mock('\Stubber\Server');

        $this->assertNull($primer->getServer());
        $this->assertSame($primer, $primer->setServer($server));
        $this->assertSame($server, $primer->getServer());
    }

    public function testPrimerCreatesMissingFolder()
    {
        $this->mockFilesystem->shouldReceive('exists')->andReturn(false);
        $this->mockFilesystem->shouldReceive('mkdir');

        $primer = new Primer($this->mockFilesystem, $this->mockSerializer);
        $this->assertNull($primer->getServer());
    }

    public function testPreparePrimer()
    {
        $host = '127.0.0.1';
        $port = 8080;

        $this->mockFilesystem->shouldReceive('exists')->andReturn(false);
        $this->mockFilesystem->shouldReceive('mkdir')->once();
        $this->mockFilesystem->shouldReceive('remove')->once();

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addDirectory('primer', '/stubber');

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('getHost')->andReturn($host);
        $server->shouldReceive('getPort')->andReturn($port);

        $primer = new Primer($this->mockFilesystem, $this->mockSerializer, 'mfs://stubber/primer');

        $primer->setServer($server);

        $primer->prepare();
    }

    public function testAddPrimedRequest()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $serializedRequest = '{"MockRequest":1}';

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addDirectory('primer', '/stubber');

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('getHost')->andReturn($host);
        $server->shouldReceive('getPort')->andReturn($port);

        $primer = new Primer(new Filesystem(), $this->mockSerializer, 'mfs://stubber/primer');
        $primer->setServer($server);
        $primer->prepare();

        $primedRequest = new PrimedRequest();

        $this->mockSerializer->shouldReceive('serialize')->andReturn($serializedRequest);

        $primer->addPrimedRequest($primedRequest);
    }

    public function testGetPrimedDataWhenDataIsEmptyAndOverrideIsFalseAndNoDataReturnedReturnsEmptyArray()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $override = false;
        $expectedPrimedData = array();

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addDirectory('primer', '/stubber');
        $mockFs->getFileSystem()->addFile($host . '-' . $port, '', '/stubber/primer');

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('getHost')->andReturn($host);
        $server->shouldReceive('getPort')->andReturn($port);

        $primer = new Primer(new Filesystem(), $this->mockSerializer, 'mfs://stubber/primer');
        $primer->setServer($server);
        $primer->prepare();

        $this->assertSame($expectedPrimedData, $primer->getPrimedData($override));
    }

    public function testGetPrimedDataWhenDataIsEmptyAndOverrideIsFalseReturnsValidResult()
    {
        $host = '127.0.0.1';
        $port = 8080;
        $override = false;
        $expectedData = 'MockData';

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addDirectory('primer', '/stubber');

        $server = Mockery::mock('\Stubber\Server');
        $server->shouldReceive('getHost')->andReturn($host);
        $server->shouldReceive('getPort')->andReturn($port);

        $primer = new Primer(new Filesystem(), $this->mockSerializer, 'mfs://stubber/primer');
        $primer->setServer($server);
        $primer->prepare();

        $mockFs->getFileSystem()->deleteFile('/stubber/primer/' . $host . '-' . $port);
        $mockFs->getFileSystem()->addFile($host . '-' . $port, 'MockData', '/stubber/primer');

        $this->mockSerializer->shouldReceive('deserialize')->andReturn($expectedData);

        $this->assertSame(array($expectedData), $primer->getPrimedData($override));
    }
}
