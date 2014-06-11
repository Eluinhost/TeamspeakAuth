<?php
namespace PublicUHC\TeamspeakAuth\Commands;

use Doctrine\ORM\EntityManager;
use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NukeGroupCommand extends Command {

    private $teamspeakHelper;
    private $em;

    public function __construct(TeamspeakHelper $teamspeakHelper, EntityManager $manager) {
        parent::__construct(null);
        $this->teamspeakHelper = $teamspeakHelper;
        $this->em = $manager;
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

        $dbIDs = $this->teamspeakHelper->getDBIdsForGroupID($groupID);

        if(count($dbIDs) == 0) {
            $output->writeln("No users in the group supplied");
            return;
        }

        $output->writeln("Removing " . count($dbIDs) . " users with the IDs:");
        $output->writeln(json_encode($dbIDs));

        $qb = $this->em->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHC\TeamspeakAuth\Entities\Authentication', 'authentication')
            ->join('authentication.teamspeakAccount', 'tsAccount')
            ->where('tsAccount.uuid = :uuid');

        foreach($dbIDs as $dbID) {
            $this->teamspeakHelper->removeDBIdFromGroup($dbID, $groupID);
            $this->teamspeakHelper->setDescriptionForDBId('', $dbID);
            $this->teamspeakHelper->removeIconForDBId($dbID);

            $uuid = $this->teamspeakHelper->getUUIDForDBId($dbID);

            $qb->setParameter('uuid', $uuid);

            $results = $qb->getQuery()->getResult();
            $output->writeln($dbID . ": (" . $uuid . ") - removing " . count($results) . " authentications");

            foreach($results as $result) {
                $this->em->remove($result);
            }
            $this->em->flush();
        }

        $output->writeln("Removed all users");
    }
}