<?php

require_once('bootstrap.php');

use Symfony\Component\Filesystem\Filesystem;
use Posix\Posix;
use Stubber\ProcessManager;
use JMS\Serializer\SerializerBuilder;
use Stubber\Primer;
use Stubber\Server;
use Stubber\Application\BasicApplication;

$filesystem = new Filesystem();
$processManager = new ProcessManager($filesystem, new Posix());
$primer = new Primer($filesystem, SerializerBuilder::create()->build());

$app = new BasicApplication(new Server($processManager, $primer));
$app->setServerHost($_GET['host']);
$app->setServerPort($_GET['port']);
$app->run();

// PRIMER FROM NOW ON
$request = new Primer\Request();
$request
    ->setMethod('GET')
    ->setPath('/')
    ->addResponseOption('status', 200)
;
$app->getServer()->getPrimer()->addPrimedRequest($request);

$request = new Primer\Request();
$request
    ->setMethod('GET')
    ->setPath('/favicon.ico')
    ->addResponseOption('status', 200)
;
$app->getServer()->getPrimer()->addPrimedRequest($request);

sleep(60);


// Done
$app->getServer()->getProcess()->kill(9);
die('DONE');