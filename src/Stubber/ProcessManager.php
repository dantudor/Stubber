<?php

namespace Stubber;

use Pagon\ChildProcess\ChildProcess;
use Pagon\ChildProcess\Process;
use Posix\Posix;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Class ProcessManager
 *
 * @package Stubber
 */
class ProcessManager extends ChildProcess
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Posix
     */
    protected $posix;

    /**
     * @var string
     */
    protected $pidFolder;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param Posix      $posix
     * @param string     $pidFolder
     */
    public function __construct(Filesystem $filesystem, Posix $posix, $pidFolder = null)
    {
        $this->filesystem = $filesystem;
        $this->posix = $posix;

        // Prepare resource and data
        $this->ppid = $this->pid = posix_getpid();
        $this->process = new Process($this, $this->pid, $this->ppid, true);
        $this->registerSigHandlers();
        $this->registerShutdownHandlers();

        if (is_null($pidFolder)) {
            $this->pidFolder = sys_get_temp_dir() . 'stubber/process';
        } else {
            $this->pidFolder = $pidFolder;
        }

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
        $this->posix->kill(file_get_contents($this->pidFolder . '/' . $host . '-' . $port), 9);
    }
}