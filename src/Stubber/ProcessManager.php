<?php

namespace Stubber;

use ProcessControl\ProcessControlService;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Class ProcessManager
 *
 * @package Stubber
 */
class ProcessManager
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ProcessControlService
     */
    protected $processControlService;

    /**
     * @var string
     */
    protected $pidFolder;

    /**
     * Constructor
     *
     * @param Filesystem            $filesystem
     * @param ProcessControlService $processControlService
     * @param null|string           $pidFolder
     */
    public function __construct(Filesystem $filesystem, ProcessControlService $processControlService, $pidFolder = null)
    {
        $this->filesystem = $filesystem;
        $this->processControlService = $processControlService;

        $this->pidFolder =  (is_null($pidFolder)) ? sys_get_temp_dir() . 'stubber/process' : $pidFolder;

        if (false === $this->filesystem->exists($this->pidFolder)) {
            $this->filesystem->mkdir($this->pidFolder, 0777, true);
        }
    }

    /**
     * Register Process ID
     *
     * @param string $host
     * @param int    $port
     * @param int    $pid
     *
     * @return $this
     */
    public function registerPid($host, $port, $pid)
    {
        if ($this->pidExists($host, $port)) {
            $this->terminatePid($host, $port);
        }

        file_put_contents($this->pidFolder . '/' . $host . '-' . $port, $pid);

        return $this;
    }

    /**
     * Process ID Exists?
     *
     * @param string $host
     * @param int    $port
     *
     * @return bool
     */
    public function pidExists($host, $port)
    {
        if ($this->filesystem->exists($this->pidFolder . '/' . $host . '-' . $port)) {
            return true;
        }

        return false;
    }

    /**
     * Terminate Process ID
     *
     * @param string $host
     * @param int    $port
     */
    public function terminatePid($host, $port)
    {
        $processFile = $this->pidFolder . '/' . $host . '-' . $port;
        if ($this->filesystem->exists($processFile)) {
            $processId = file_get_contents($this->pidFolder . '/' . $host . '-' . $port);

            if ($this->processControlService->getMaster()->hasChildById($processId)) {
                $this->processControlService->terminateProcess(
                    $this->processControlService->getMaster()->getChildById($processId)
                );
            }

            $this->filesystem->remove($processFile);
        }
    }
}