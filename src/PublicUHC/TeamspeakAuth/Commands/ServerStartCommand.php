<?php
namespace PublicUHC\TeamspeakAuth\Commands;


use Doctrine\ORM\EntityManager;
use PublicUHC\MinecraftAuth\AuthServer\AuthServer;
use PublicUHC\MinecraftAuth\Protocol\Packets\DisconnectPacket;
use PublicUHC\MinecraftAuth\Protocol\Packets\StatusResponsePacket;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStartCommand extends Command {

    private $server;
    private $em;
    private $description;

    public function __construct(AuthServer $server, EntityManager $em, $description)
    {
        parent::__construct(null);
        $this->server = $server;
        $this->em = $em;
        $this->description = $description;
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
            $output->write("USER LOGIN: $username / $uuid");
            //TODO db stuffs
            $packet->setReason("USERNAME: $username UUID: $uuid");
        });

        $description = $this->description;

        //TODO read the favicon from filesystem and set data URI
        $favicon = null;

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