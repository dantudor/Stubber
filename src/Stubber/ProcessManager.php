<?php

namespace Stubber;

use ProcessControl\Process;
use ProcessControl\ProcessControlService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class ProcessManager
 *
 * @package Stubber
 */
class ProcessManager extends ProcessControlService
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var string
     */
    protected $pidFolder;

    /**
     * Constructor
     *
     * @param Filesystem            $filesystem
     * @param Finder                $finder
     * @param null|string           $pidFolder
     */
    public function __construct(Filesystem $filesystem, Finder $finder, $pidFolder = null)
    {
        $this->filesystem = $filesystem;
        $this->finder = $finder;

        $this->pidFolder = (is_null($pidFolder)) ? sys_get_temp_dir() . 'stubber/process' : $pidFolder;

        if (false === $this->filesystem->exists($this->pidFolder)) {
            $this->filesystem->mkdir($this->pidFolder, 0777, true);
        }

        parent::__construct();

        $this->hydrateProcessesFromFile();
    }

    /**
     * Hydrate Processes From File
     *
     * @return ProcessManager
     */
    protected function hydrateProcessesFromFile()
    {
        $fileProcesses = $this->finder->files()->in($this->pidFolder);

        foreach ($fileProcesses as $fileProcess) {
            $processId = file_get_contents($fileProcess);
            if (false === $this->master->hasChildById($processId)) {
                $child = new Process($processId, $this->getMaster());
                $this->getMaster()->addChild($child);
            }
        }

        return $this;
    }

    /**
     * Register Process ID
     *
     * @param Process $process
     * @param string  $host
     * @param int     $port
     *
     * @return ProcessManager
     */
    public function registerProcess(Process $process, $host, $port)
    {
        if ($this->getMaster()->hasChildById($process->getId())) {
            $this->terminateProcess($process, $host, $port);
        }

        file_put_contents($this->pidFolder . '/' . $host . ':' . $port, $process->getId());

        return $this;
    }

    /**
     * Terminate Process
     *
     * @param Process $process
     * @param int $signal
     *
     * @return ProcessManager
     */
    public function terminateProcess(Process $process, $signal = SIGKILL)
    {
        $fileProcesses = $this->finder->files()->in($this->pidFolder);
        foreach ($fileProcesses as $fileProcess) {
            $processId = file_get_contents($fileProcess);
            if ($process->getId() == $processId) {
                $this->filesystem->remove($fileProcess->getPathname());
            }
        }

        return parent::terminateProcess($process, $signal);
    }
}