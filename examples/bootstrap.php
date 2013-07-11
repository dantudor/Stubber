<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

AnnotationRegistry::registerAutoloadNamespace('JMS\Serializer\Annotation', __DIR__ . '/../vendor/jms/serializer/src');