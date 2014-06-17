<?php
namespace PublicUHC\TeamspeakAuth\Commands;


use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use PublicUHC\MinecraftAuth\AuthServer\AuthServer;
use PublicUHC\MinecraftAuth\Protocol\Packets\DisconnectPacket;
use PublicUHC\MinecraftAuth\Protocol\Packets\StatusResponsePacket;
use PublicUHC\TeamspeakAuth\Entities\MinecraftAccount;
use PublicUHC\TeamspeakAuth\Entities\MinecraftCode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStartCommand extends Command {

    private $server;
    private $em;
    private $description;
    private $faviconLocation;

    public function __construct(AuthServer $server, EntityManager $em, $description, $faviconLocation)
    {
        parent::__construct(null);
        $this->server = $server;
        $this->em = $em;
        $this->description = $description;
        $this->faviconLocation = $faviconLocation;
    }

    protected function configure()
    {
        $this->setName('server:start')
            ->setDescription('Runs the authentication server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Starting server... You can stop the server with Ctrl+C.");

        $this->server->on('login_success', function($username, $uuid, DisconnectPacket $packet) use ($output) {
            $output->write("USER LOGIN: $username / $uuid\n");

            $qb = $this->em->createQueryBuilder();
            $qb->select('account')
                ->from('PublicUHC\TeamspeakAuth\Entities\MinecraftAccount', 'account')
                ->where('account.uuid = :uuid')
                ->setParameter('uuid', $uuid);

            try {
                /** @var $account MinecraftAccount */
                $account = $qb->getQuery()->getSingleResult();
            } catch (NoResultException $ex) {
                echo "New account created for $username\n";
                $account = new MinecraftAccount();
            }

            $account->setName($username)->setUUID($uuid)->setUpdatedAt(new DateTime('now'));

            $code = new MinecraftCode();
            $code->setAccount($account);

            $codes = $account->getCodes();
            $codes->clear();
            $codes->add($code);

            $this->em->persist($account);
            $this->em->persist($code);
            $this->em->flush();

            //detach so changes to db externally will affet the account if it's used again
            //also stops memory leaking due to references being kept
            $this->em->detach($account);
            $this->em->detach($code);

            $packet->setReason('Your code is '.$code->getCode());
        });

        $description = $this->description;

        $imageData = base64_encode(file_get_contents($this->faviconLocation));
        $favicon = 'data:image/png;base64,'.$imageData;

        $this->server->on('status_request', function(StatusResponsePacket $packet) use ($description, $favicon) {
            $packet->setDescription($description)
                ->setMaxPlayers(-1)
                ->setOnlineCount(-1)
                ->setVersion('1.7.6+')
                ->setFavicon($favicon);
        });

        $this->server->start();
    }
} 