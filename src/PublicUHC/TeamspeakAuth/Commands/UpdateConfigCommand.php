<?php
namespace PublicUHC\TeamspeakAuth\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class UpdateConfigCommand extends Command {

    private static $questionmap = [
        'minutesToLast' => [
            'question' => 'How many minutes should codes last for?',
            'type' => 'integer'
        ],
        'siteName' => [
            'question' => 'Site name for website'
        ],
        'skinCacheTime' => [
            'question' => 'How many seconds should skins be cached for?',
            'type' => 'integer'
        ],
        'serverAddress' => [
            'question' => 'What Minecraft server address(es) should the website show for people to connect to?'
        ],
        'teamspeak.host' => [
            'question' => 'Teamspeak server address'
        ],
        'teamspeak.port' => [
            'question' => 'Teamspeak server port',
            'type' => 'integer'
        ],
        'teamspeak.query_port' => [
            'question' => 'Teamspeak server query port',
            'type' => 'integer'
        ],
        'teamspeak.username' => [
            'question' => 'Teamspeak server query account name',
        ],
        'teamspeak.password' => [
            'question' => 'Teamspeak server query password',
            'type' => 'password'
        ],
        'teamspeak.group_id' => [
            'question' => 'Teamspeak group ID to add users to',
            'type' => 'integer'
        ],
        'database.host' => [
            'question' => 'Database server address',
        ],
        'database.port' => [
            'question' => 'Database server port',
            'type' => 'integer'
        ],
        'database.database' => [
            'question' => 'Database database',
        ],
        'database.username' => [
            'question' => 'Database account username',
        ],
        'database.password' => [
            'question' => 'Database account password',
            'type' => 'password'
        ],
        'database.keepAlive' => [
            'question' => 'How long between keep alive queries for the database when running the Auth Server?',
            'type' => 'integer'
        ],
        'minecraft.host' => [
            'question' => 'Address for Minecraft server to bind to',
        ],
        'minecraft.port' => [
            'question' => 'Port for Minecraft server to bind to',
            'type' => 'integer'
        ],
        'minecraft.description' => [
            'question' => 'Description to show on the Minecraft server list'
        ],
    ];

    protected function configure()
    {
        $this->setName('config:update')
            ->setDescription('Update your config file to latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFileLocation = 'config/config.yml';
        $distFileLocation = 'src/config.yml.dist';

        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

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

        foreach($difference as $key => $value) {
            $questionDetails = isset(self::$questionmap[$key]) ? self::$questionmap[$key] : [];
            if(!isset($questionDetails['question']))
                $questionDetails['question'] = $key;
            if(!isset($questionDetails['type']))
                $questionDetails['type'] = 'string';

            $question = new Question("<question>{$questionDetails['question']} ($value):</question>", $value);

            switch($questionDetails['type']) {
                case 'password':
                    $question->setHidden(true); $question->setHiddenFallback(true); break;
                case 'integer':
                    $question->setValidator(function($value) {
                        if((!ctype_digit(strval($value)))) {
                            throw new \RuntimeException('Value must be a positive integer');
                        }
                        return $value;
                    });
            }

            $answer = $questionHelper->ask($input, $output, $question);
            if(!$question->isHidden()) {
                $output->writeln("<info> $answer");
            }
            $new[$key] = $answer;
        }

        //set the new parameters
        $configYML['parameters'] = array_merge($configYML['parameters'], $new);

        //copy all of the services across directly
        $configYML['services'] = $distYML['services'];

        //write the new config file
        file_put_contents($configFileLocation, Yaml::dump($configYML, 3, 2));

        $command = $this->getApplication()->find('clean:container');
        $command->run($input, $output);

        $output->writeln('<info>Config file up to date!</info>');
    }
} 