<?php

use PublicUHC\TeamspeakAuth\Kernel\ProjectApplication;
use PublicUHC\TeamspeakAuth\Kernel\ProjectFramework;

require_once __DIR__ . '/../vendor/autoload.php';

set_time_limit(0);

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;

$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');
$debug = getenv('SYMFONY_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

if ($debug) {
    Debug::enable();
}

$kernel = new ProjectFramework($env, $debug);
$application = new ProjectApplication($kernel);
$application->run($input);