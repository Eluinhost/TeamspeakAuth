<?php
namespace PublicUHC\TeamspeakAuth\Commands;

use Doctrine\ORM\EntityManager;
use Exception;
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
        $this->setName('group:nuke')
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

        if($groupID == "*") {
            $dbIDs = $this->teamspeakHelper->getAllDBIds();
        } else {
            $dbIDs = $this->teamspeakHelper->getDBIdsForGroupID($groupID);
        }

        if(count($dbIDs) == 0) {
            $output->writeln('<error>No users in the group supplied</error>');
            return;
        }

        $output->writeln('<info>Removing ' . count($dbIDs) . ' users with the IDs:</info>');
        $output->writeln('<info>'.json_encode($dbIDs).'</info>');

        $qb = $this->em->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHC\TeamspeakAuth\Entities\Authentication', 'authentication')
            ->join('authentication.teamspeakAccount', 'tsAccount')
            ->where('tsAccount.uuid = :uuid');

        foreach($dbIDs as $dbID) {
            try {
                $this->teamspeakHelper->removeDBIdFromGroup($dbID, $groupID);
            }catch(Exception $ex){}
            $this->teamspeakHelper->setDescriptionForDBId('', $dbID);
            try {
                $this->teamspeakHelper->removeIconForDBId($dbID);
            }catch (Exception $ex){}
            $uuid = $this->teamspeakHelper->getUUIDForDBId($dbID);

            $qb->setParameter('uuid', $uuid);

            $results = $qb->getQuery()->getResult();
            $output->writeln("<info>$dbID: ($uuid) - removing " . count($results) . " authentications</info>");

            foreach($results as $result) {
                $this->em->remove($result);
            }
            $this->em->flush();
        }

        $output->writeln("<comment>Removed all users</comment>");
    }
}