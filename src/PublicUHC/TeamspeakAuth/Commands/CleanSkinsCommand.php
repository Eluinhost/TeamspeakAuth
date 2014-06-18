<?php
namespace PublicUHC\TeamspeakAuth\Commands;

class CleanSkinsCommand extends CleanCommand {

    protected function configure()
    {
        $this->setName('clean:skins')
            ->setDescription('Cleans the cached skins');
    }

    /**
     * @return String path to the folder to clean
     */
    protected function getFolder()
    {
        return '/cache/skins';
    }
}