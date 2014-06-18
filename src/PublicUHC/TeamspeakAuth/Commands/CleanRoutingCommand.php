<?php
namespace PublicUHC\TeamspeakAuth\Commands;

class CleanRoutingCommand extends CleanCommand {

    protected function configure()
    {
        $this->setName('clean:router')
            ->setDescription('Cleans the cached routing (to be ran after routes.yml changes)');
    }

    /**
     * @return String path to the folder to clean
     */
    protected function getFolder()
    {
        return '/cache/routing';
    }
}