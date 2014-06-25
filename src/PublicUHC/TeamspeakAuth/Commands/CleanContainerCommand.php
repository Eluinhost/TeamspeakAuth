<?php
namespace PublicUHC\TeamspeakAuth\Commands;

class CleanContainerCommand extends CleanCommand {

    private $env;

    public function __construct($base_path, $environment)
    {
        parent::__construct($base_path);
        $this->env = $environment;
    }

    protected function configure()
    {
        $this->setName('clean:container')
            ->setDescription('Cleans the current environments cached container');
    }

    /**
     * @return String path to the folder to clean
     */
    protected function getFolder()
    {
        return '/cache/' . $this->env;
    }
}