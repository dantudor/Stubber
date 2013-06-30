<?php

namespace Stubber\Command;

use Stubber\Service\ProcessService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stubber\Server\BasicServer;
USE Stubber\Exception\SocketConnectionException;

/**
 * Class StopCommand
 *
 * @package Stubber\Command
 */
class StopCommand extends Command
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
            ->setName('stubber:stop')
            ->setDescription('Stop a Stubber Server')
            ->addArgument('host', InputArgument::REQUIRED, 'What hostname should the server use?')
            ->addArgument('port', InputArgument::REQUIRED, 'What port should the server run on?')
            ->addArgument('pidFolder', InputArgument::OPTIONAL, 'Folder to store the process references', $defaultPidFolder);
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
        $host = $input->getArgument('host');
        $port = $input->getArgument('port');

        $processService = new ProcessService($input->getArgument('pidFolder'));

        if (true === $processService->serverExists($host, $port)) {
            $output->writeln('<info>Stubber process located and killed.</info>');
            $processService->kill($host, $port);
        } else {
            $output->writeln('<info>Stubber process not located</info>');
        }
    }
}