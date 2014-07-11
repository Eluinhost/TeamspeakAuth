<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Command;


use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use PublicUHC\MinecraftAuth\AuthServer\AuthServer;
use PublicUHC\MinecraftAuth\Protocol\Packets\DisconnectPacket;
use PublicUHC\MinecraftAuth\Protocol\Packets\StatusResponsePacket;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftCode;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStartCommand extends Command {

    private $server;
    private $em;
    private $loop;
    private $keepAlive;
    private $description;
    private $faviconLocation;

    public function __construct(AuthServer $server, EntityManager $em, LoopInterface $loop, $keepAlive, $description, $faviconLocation)
    {
        parent::__construct(null);
        $this->server = $server;
        $this->em = $em;
        $this->loop = $loop;
        $this->keepAlive = $keepAlive;
        $this->description = $description;
        $this->faviconLocation = $faviconLocation;
    }

    protected function configure()
    {
        $this->setName('authserver:start')
            ->setDescription('Runs the authentication server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting server... You can stop the server with Ctrl+C.</info>");

        //setup a loop to keep the connection alive
        $this->loop->addPeriodicTimer($this->keepAlive, function() {
            $this->em->getConnection()->executeQuery('DO 1');
        });


        $this->server->on('login_success', function($username, $uuid, DisconnectPacket $packet) use ($output) {
            $qb = $this->em->createQueryBuilder();
            $qb->select('account')
                ->from('PublicUHCTeamspeakAuthBundle:MinecraftAccount', 'account')
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
            $output->writeln("<comment>USERNAME: $username UUID: $uuid CODE: {$code->getCode()}</comment>");
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