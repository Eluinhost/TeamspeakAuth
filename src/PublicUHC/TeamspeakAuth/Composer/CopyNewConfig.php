<?php
namespace PublicUHC\TeamspeakAuth\Composer;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Symfony\Component\Yaml\Yaml;

class CopyNewConfig {

    public static function postInstallCommand(Event $event)
    {
        self::updateConfigFile($event->getIO());
    }

    public static function postUpdateCommand(Event $event)
    {
        self::updateConfigFile($event->getIO());
    }

    public static function updateConfigFile(IOInterface $interface)
    {
        $configFileLocation = 'config/config.yml';
        $distFileLocation = 'src/config.yml.dist';

        //make the config file if it doesn't exist
        if(!is_file($configFileLocation)) {
            touch($configFileLocation);
        }

        //parse the YML files
        $configYML = Yaml::parse($configFileLocation, true);
        $distYML = Yaml::parse($distFileLocation, true);

        //set the parameters array if it doesn't exist
        if(!isset($configYML['parameters'])) {
            $configYML['parameters'] = [];
        }

        //get a list of all the parameters not set in the config file
        $difference = array_diff_key($distYML['parameters'], $configYML['parameters']);

        $new = [];

        foreach($difference as $upperLevelKey => $upperLevelValue) {
            if(is_array($upperLevelValue)) {
                foreach($upperLevelValue as $subLevelKey => $subLevelValue) {
                    $answer = $interface->ask("$upperLevelKey.$subLevelKey ($subLevelValue):", $subLevelValue);
                    $new["$upperLevelKey.$subLevelKey"] = $answer;
                }
            } else {
                $answer = $interface->ask("$upperLevelKey ($upperLevelValue):", $upperLevelValue);
                $new[$upperLevelKey] = $answer;
            }
        }

        foreach($new as $key=>$value) {
            $interface->write("$key = $value");
        }

        //TODO ask for new values e.t.c.

        //TODO set the configYML parameters

        //copy the services across directly
        $configYML['services'] = $distYML['services'];

        //write the new config file
        //file_put_contents($configFileLocation, Yaml::dump($configYML, 3, 2));
    }
} 