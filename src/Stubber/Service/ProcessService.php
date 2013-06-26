<?php

namespace Stubber\Service;

use Stubber\Exception\ProcessDirectoryException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProcessService
 *
 * @package Stubber\Service
 */
class ProcessService
{
    /**
     * @var string
     */
    protected $pidFolder;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $processes;

    /**
     * Constructor
     *
     * @param $pidFolder
     * @throws ProcessDirectoryException
     */
    public function __construct($pidFolder)
    {
        $this->pidFolder = (string) $pidFolder;
        $this->filesystem = new Filesystem();

        if (false === $this->filesystem->exists($this->pidFolder)) {
            try {
                $this->filesystem->mkdir($this->pidFolder);
            } catch (IOException $e) {
                throw new ProcessDirectoryException(sprintf('Could not create new pid folder (%s)', $this->pidFolder));
            }
        }

        if (!is_writable($this->pidFolder)) {
            throw new ProcessDirectoryException(sprintf('Pid folder is not writable (%s)', $this->pidFolder));
        }
    }

    /**
     * Server Exists?
     *
     * @param string  $host Server host
     * @param integer $port Server port
     *
     * @return bool
     */
    public function serverExists($host, $port)
    {
        if (null !== $this->getPidForServer($host, $port)) {
            return true;
        }

        return false;
    }

    /**
     * Get pid for server
     *
     * @param $host
     * @param $port
     *
     * @return string
     */
    protected function getPidForServer($host, $port)
    {
        $serverFile = $this->pidFolder . DIRECTORY_SEPARATOR . $host . ':' . $port;

        if (true === $this->filesystem->exists($serverFile)) {
            return file_get_contents($serverFile);
        }
    }

    /**
     * Add a new process under management
     *
     * @param string $host Host
     * @param int    $port Port
     * @param string $pid  Process Id
     *
     * @return ProcessManager
     */
    public function add($host, $port, $pid)
    {
        $serverFile = $this->pidFolder . DIRECTORY_SEPARATOR . $host . ':' . $port;
        file_put_contents($serverFile, $pid);

        return $this;
    }

    /**
     * Kill
     *
     * @param string $host Host
     * @param int    $port Port
     *
     * @return ProcessService
     */
    public function kill($host, $port)
    {
        $serverFile = $this->pidFolder . DIRECTORY_SEPARATOR . $host . ':' . $port;

        $pid = $this->getPidForServer($host, $port);
        if (null !== $pid) {
            $this->filesystem->remove($serverFile);
            posix_kill($pid, 9);
        }

        return $this;
    }
}