<?php
namespace PublicUHC\TeamspeakAuth\Composer;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Symfony\Component\Yaml\Yaml;

class CopyNewConfig {

    private static $questionmap = [
        'minutesToLast' => 'How many minutes should codes last for?',
        'skinCacheTime' => 'How many seconds should skins be cached for?',
        'serverAddress' => 'What Minecraft server address should the website show for people to connect to?',
        'teamspeak.host' => 'Teamspeak server address',
        'teamspeak.port' => 'Teamspeak server port',
        'teamspeak.username' => 'Teamspeak server query account name',
        'teamspeak.password' => 'Teamspeak server query password',
        'teamspeak.group_id' => 'Teamspeak group ID to add users to',
        'database.host' => 'Database server address',
        'database.port' => 'Database server port',
        'database.database' => 'Database database',
        'database.username' => 'Database account username',
        'database.password' => 'Database account password',
        'minecraft.host' => 'Address for Minecraft server to bind to',
        'minecraft.port' => 'Port for Minecraft server to bind to',
        'minecraft.description' => 'Description to show on the Minecraft server list'
    ];

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
                    $key = "$upperLevelKey.$subLevelKey";
                    $question = isset(self::$questionmap[$key]) ? self::$questionmap[$key] : $key;
                    $answer = $interface->ask($question." ($subLevelValue):", $subLevelValue);
                    $new["$upperLevelKey.$subLevelKey"] = $answer;
                }
            } else {
                $key = $upperLevelKey;
                $question = isset(self::$questionmap[$key]) ? self::$questionmap[$key] : $key;
                $answer = $interface->ask($question." ($upperLevelValue):", $upperLevelValue);
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