<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Helpers;

use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount;
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
     * Get the database ID for the given UUID
     * @param $uuid string the uuid to look for
     * @return int|false the database ID if found or false otherwise
     */
    public function getClientDBId($uuid);

    /**
     * Set the description for the client database id
     * @param $description string the description to set
     * @param $cldbid int the database id for the client
     */
    public function setDescriptionForDBId($description, $cldbid);

    /**
     * Set the description for the client with the given UUID
     * @param $description string the description to set
     * @param $uuid string the client's UUID
     */
    public function setDescriptionForUUID($description, $uuid);

    /**
     * Add the user with the given database ID to the group with the given ID
     * @param $cldbid int the database ID of the user
     * @param $groupID int the ID of the group to add to
     */
    public function addDBIdToGroup($cldbid, $groupID);

    /**
     * Add the user with the given UUID to the group with the given ID
     * @param $uuid string the UUID of the user
     * @param $groupID int the ID of the group to add to
     */
    public function addUUIDToGroup($uuid, $groupID);

    /**
     * Remove the user with the given database ID from the group with the given ID
     * @param $cldbid int the database ID of the user
     * @param $groupID int the ID of the group to remove from
     */
    public function removeDBIdFromGroup($cldbid, $groupID);

    /**
     * Remove the user with the given UUID from the group with the given ID
     * @param $uuid int the UUID of the user
     * @param $groupID int the ID of the group to remove from
     */
    public function removeUUIDFromGroup($uuid, $groupID);

    /**
     * Set the icon for the user
     * @param $data string the image data to set to
     * @param $cldbid int the user's database ID
     * @return true if complete, false on error
     */
    public function setIconForDBId($cldbid, $data);

    /**
     * Set the icon for the user
     * @param $data string the image data to set to
     * @param $uuid string the user's UUID
     * @return true if complete, false on error
     */
    public function setIconForUUID($uuid, $data);

    /**
     * Remove the client icon for the user
     * @param $cldbid int the dbid of the user
     */
    public function removeIconForDBId($cldbid);

    /**
     * Remove the client icon for the user
     * @param $uuid string the uuid of the user
     */
    public function removeIconForUUID($uuid);

    /**
     * Undoes the authentication supplied
     * @param Authentication $authentication the authentication to undo
     */
    public function unauthenticate(Authentication $authentication);

    /**
     * Check if the teamspeak user with the given UUID is online
     * @param $uuid string the UUID to check
     * @return boolean
     */
    public function isUUIDOnline($uuid);

    /**
     * @param $groupID int the group ID to look for
     * @return int[] a list of DBIDs in the given group
     */
    public function getDBIdsForGroupID($groupID);

    /**
     * @param $cldbid int the client DBId
     * @return String uuid
     */
    public function getUUIDForDBId($cldbid);

    /**
     * @return int[] a list of all DBIds
     */
    public function getAllDBIds();
} 