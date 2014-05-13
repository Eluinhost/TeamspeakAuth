<?php

namespace com\publicuhc\ts3auth;

use TeamSpeak3;
use TeamSpeak3_Adapter_ServerQuery_Exception;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class TeamspeakHelper {

    private $host;
    private $queryPort;
    private $serverPort;
    private $username;
    private $password;

    public function __construct($host, $queryPort, $serverPort, $username, $password = '') {
        $this->host = $host;
        $this->queryPort = $queryPort;
        $this->serverPort = $serverPort;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Return the UUID for the given name
     * @param $name string the name to search for
     * @return TeamSpeak3_Node_Client|null the client if found, null otherwise
     */
    public function getClientForName($name) {
        $server = self::getServerInstance();
        $client = null;
        try{
            $client = $server->clientGetByName($name);
        }catch (TeamSpeak3_Adapter_ServerQuery_Exception $ignored){}

        return $client;
    }

    public function getUUIDForClient(TeamSpeak3_Node_Client $client) {
        return $client->infoDb()['client_unique_identifier'];
    }

    /**
     * @return TeamSpeak3_Node_Server
     */
    public function getServerInstance() {
        return TeamSpeak3::factory("serverquery://{$this->username}:{$this->password}@{$this->host}:{$this->queryPort}/?server_port={$this->serverPort}");
    }
} 