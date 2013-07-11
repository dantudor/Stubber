<?php

namespace Stubber;

use Stubber\Exception\PrimerMissingException;
use Stubber\Primer\Request as PrimedRequest;
use JMS\Serializer\Serializer;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Primer
 *
 * @package Stubber
 */
class Primer
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \JMS\Serializer\Serializer
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $primerFolder;

    /**
     * @var string
     */
    protected $primerFile;

    /**
     * @var array[PrimedRequest]
     */
    protected $primedData = array();

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var Server
     */
    protected $server;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param Serializer $serializer
     * @param string     $primerFolder
     */
    public function __construct(Filesystem $filesystem, Serializer $serializer, $primerFolder = null)
    {
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;

        if (is_null($primerFolder)) {
            $this->primerFolder = sys_get_temp_dir() . 'stubber/prime';
        } else {
            $this->primerFolder = $primerFolder;
        }

        if (false === $this->filesystem->exists($this->primerFolder)) {
            $this->filesystem->mkdir($this->primerFolder, 0777, true);
        }
    }

    /**
     * Set Server
     *
     * @param Server $server
     *
     * @return Primer
     */
    public function setServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get Server
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Prepare
     *
     * @return Primer
     */
    public function prepare()
    {
        $this->primerFile = $this->primerFolder . '/' . $this->getServer()->getHost() . '-' . $this->getServer()->getPort();

        $this->filesystem->remove($this->primerFile);
        file_put_contents($this->primerFile, '');

        return $this;
    }

    /**
     * Add Primed Request
     *
     * @param PrimedRequest $request
     *
     * @return $this
     */
    public function addPrimedRequest(PrimedRequest $request)
    {
        file_put_contents($this->primerFile, $this->serializer->serialize($request, 'json') . PHP_EOL, FILE_APPEND);

        return $this;
    }

    /**
     * Is Primed
     *
     * @return bool
     */
    public function isPrimed()
    {
        $primedData = $this->getPrimedData();

        return empty($primedData) ? false : true;
    }

    /**
     * Retrieve primed data
     *
     * @param bool $override
     *
     * @return bool
     */
    public function getPrimedData($override = false)
    {
        if (true === empty($this->primedData) || true === $override) {
            $primedDataArray = explode(PHP_EOL, file_get_contents($this->primerFile));
            foreach ($primedDataArray as $primedData) {
                if (false === empty($primedData)) {
                    $this->primedData[] = $this->serializer->deserialize($primedData, 'Stubber\Primer\Request' , 'json');
                }
            }
        }

        return $this->primedData;
    }

    /**
     * Get next primed request
     *
     * @return PrimedRequest|null
     *
     * @throws PrimerMissingException
     */
    public function getNextPrimedRequest()
    {
        if (false === array_key_exists($this->position, $this->primedData)) {
            throw new PrimerMissingException('The primed request was not found');
        }

        $primedData = $this->primedData[$this->position];
        $this->position++;

        return $primedData;
    }

}