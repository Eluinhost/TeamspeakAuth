<?php
namespace PublicUHC\TeamspeakAuth\Commands;

use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NukeGroupCommand extends Command {

    private $teamspeakHelper;

    public function __construct(TeamspeakHelper $teamspeakHelper) {
        parent::__construct(null);
        $this->teamspeakHelper = $teamspeakHelper;
    }

    protected function configure()
    {
        $this->setName('nuke:group')
            ->setDescription('Nuke the group with the given ID')
            ->addArgument(
                'groupID',
                InputArgument::REQUIRED,
                'What is the ID of the group you want to nuke?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $groupID = $input->getArgument('groupID');

        //TODO remove group, icon + description

        $output->writeln(json_encode($this->teamspeakHelper->getDBIdsForGroupID($groupID)));
    }
}