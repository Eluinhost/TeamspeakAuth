<?php
namespace PublicUHC\TeamspeakAuth\Commands;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSchemaCommand extends Command {

    public function __construct(EntityManager $em)
    {
        parent::__construct(null);
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setName('schema:update')
            ->setDescription('Update the database schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Updating database schema...');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($this->em->getConnection()),
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($this->em)
        ));

        $tool = new SchemaTool($this->em);

        $tool->updateSchema($this->em->getMetadataFactory()->getAllMetadata());

        $output->write('Update complete');
    }
} 