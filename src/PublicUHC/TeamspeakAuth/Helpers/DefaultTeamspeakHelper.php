<?php

namespace PublicUHC\TeamspeakAuth\Helpers;

use TeamSpeak3;
use TeamSpeak3_Adapter_ServerQuery_Exception;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class DefaultTeamspeakHelper implements TeamspeakHelper {

    private $server;

    public function __construct(TeamSpeak3_Node_Server $server) {
        $this->server = $server;
    }

    public function getClientForName($name) {
        $server = $this->getServerInstance();
        $client = null;
        try{
            $client = $server->clientGetByName($name);
        }catch (TeamSpeak3_Adapter_ServerQuery_Exception $ignored){}

        return $client;
    }

    public function getUUIDForClient(TeamSpeak3_Node_Client $client) {
        return ''.$client->infoDb()['client_unique_identifier'];
    }

    public function getServerInstance() {
        return $this->server;
    }
} 