<?php

namespace Stubber\Command;

use Stubber\Application\BasicApplication;
use Stubber\Server;
use Stubber\Service\ProcessService;
use Stubber\Service\ServerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stubber\Server\BasicServer;
USE Stubber\Exception\SocketConnectionException;

/**
 * Class StartCommand
 *
 * @package Stubber\Command
 */
class StartCommand extends Command
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Configure
     */
    protected function configure()
    {
        $defaultPidFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Stubber';

        $this
            ->setName('stubber:start')
            ->setDescription('Start a new Stubber Server')
            ->addArgument('host', InputArgument::REQUIRED, 'What hostname should the server use?')
            ->addArgument('port', InputArgument::REQUIRED, 'What port should the server run on?')
            ->addArgument('pidFolder', InputArgument::OPTIONAL, 'Folder to store the process references', $defaultPidFolder)
        ;
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  Console Input
     * @param OutputInterface $output Console Output
     *
     * @return int|null|void
     * @throws ServerPortInUseException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting new Stubber process</info>');

        $server = new Server(new ProcessService($input->getArgument('pidFolder')));
        $basicApplication = new BasicApplication(
            $input->getArgument('host'),
            $input->getArgument('port'),
            $server
        );

        $basicApplication->run();
    }
}