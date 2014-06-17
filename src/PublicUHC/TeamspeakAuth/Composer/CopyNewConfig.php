<?php
namespace PublicUHC\TeamspeakAuth\Composer;

use Composer\Script\Event;
use Symfony\Component\Yaml\Yaml;

class CopyNewConfig {

    public static function postInstallCommand(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        $configFileLocation = $extras['ymlinstall']['config'];
        $distFileLocation = $extras['ymlinstall']['dist'];

        //make the file if it doesn't exist
        if(!is_file($configFileLocation)) {
            touch($configFileLocation);
        }

        //parse the YML files
        $configYML = Yaml::parse($configFileLocation, true);
        $distYML = Yaml::parse($distFileLocation, true);

        //TODO ask for each parameter that doesn't exist and only copy those that don't exist
        $configYML['parameters'] = $distYML['parameters'];

        //copy the services across directly
        $configYML['services'] = $distYML['services'];

        file_put_contents($configFileLocation, Yaml::dump($configYML, 3, 2));
    }
} 