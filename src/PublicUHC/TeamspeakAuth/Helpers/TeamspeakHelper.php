<?php

namespace PublicUHC\TeamspeakAuth\Helpers;

use PublicUHC\TeamspeakAuth\Entities\MinecraftAccount;
use PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

interface TeamspeakHelper {

    /**
     * Adds an 'authentication' between the two accounts and updates teamspeak to show status
     * @param TeamspeakAccount $tsAccount the teamspeak account
     * @param MinecraftAccount $mcAccount the minecraft account
     */
    public function verifyClient(TeamspeakAccount $tsAccount, MinecraftAccount $mcAccount);

    /**
     * @param TeamSpeak3_Node_Client $client
     * @return TeamspeakAccount the updated account
     */
    public function updateLastClientUsername(TeamSpeak3_Node_Client $client);

    /**
     * Return the client for the given name
     * @param $name string the name to search for
     * @return TeamSpeak3_Node_Client|null the client if found, null otherwise
     */
    public function getClientForName($name);

    /**
     * Get the uuid from the given client
     * @param TeamSpeak3_Node_Client $client
     * @return string the uuid
     */
    public function getUUIDForClient(TeamSpeak3_Node_Client $client);

    /**
     * Get the instance of the server
     * @return TeamSpeak3_Node_Server
     */
    public function getServerInstance();

    /**
     * Set the icon for the given client
     * @param TeamSpeak3_Node_Client $client the client to modify
     * @param $icon
     * @return true if successful, false if something failed
     */
    public function setClientIcon(TeamSpeak3_Node_Client $client, $icon);

    /**
     * Set the description for the given client
     * @param TeamSpeak3_Node_Client $client the client to modify
     * @param $description string the description to show
     */
    public function setClientDescription(TeamSpeak3_Node_Client $client, $description);

    /**
     * Get the client by their UUID
     * @param $uuid string the uuid to look for
     * @return mixed
     */
    public function getClientByUUID($uuid);
} 