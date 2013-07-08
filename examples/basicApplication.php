<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$processService = new Spork\ProcessManager();

$server = new \Stubber\Server($processService);

$app = new \Stubber\Application\BasicApplication($server);
$app->setServerHost($_GET['host']);
$app->setServerPort($_GET['port']);
$app->run();