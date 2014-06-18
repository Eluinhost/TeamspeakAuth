<?php
namespace PublicUHC\TeamspeakAuth\Commands;

class CleanTemplatesCommand extends CleanCommand {

    protected function configure()
    {
        $this->setName('clean:templates')
            ->setDescription('Cleans the cached templates (to be ran after changes to files in the templates folder)');
    }

    /**
     * @return String path to the folder to clean
     */
    protected function getFolder()
    {
        return '/cache/templates';
    }
}