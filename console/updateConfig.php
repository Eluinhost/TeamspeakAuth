<?php

use Composer\IO\ConsoleIO;
use PublicUHC\TeamspeakAuth\Composer\CopyNewConfig;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__.'/../vendor/autoload.php';

CopyNewConfig::updateConfigFile(
    new ConsoleIO(
        new ArgvInput(),
        new ConsoleOutput(),
        new HelperSet([
            new \Composer\Command\Helper\DialogHelper()
        ])
    )
);