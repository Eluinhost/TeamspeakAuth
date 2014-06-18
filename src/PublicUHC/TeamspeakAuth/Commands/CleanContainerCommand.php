<?php
namespace PublicUHC\TeamspeakAuth\Commands;

class CleanContainerCommand extends CleanCommand {

    protected function configure()
    {
        $this->setName('clean:container')
            ->setDescription('Cleans the cached container (to be ran after config changes)');
    }

    /**
     * @return String path to the folder to clean
     */
    protected function getFolder()
    {
        return '/cache/container';
    }
}