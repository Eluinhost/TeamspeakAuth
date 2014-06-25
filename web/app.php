<?php

use PublicUHC\TeamspeakAuth\Kernel\ProjectFramework;
use Symfony\Component\HttpFoundation\Request;

$loader = require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();
$projectRoot = dirname(dirname(__FILE__));

$kernel = new ProjectFramework('prod', false);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);