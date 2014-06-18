<?php
namespace PublicUHC\TeamspeakAuth\Commands;

class CleanCacheCommand extends CleanCommand {

    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Cleans all caches');
    }

    /**
     * @return String path to the folder to clean
     */
    protected function getFolder()
    {
        return '/cache';
    }
}