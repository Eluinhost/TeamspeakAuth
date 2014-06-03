<?php

namespace PublicUHC\TeamspeakAuth\Helpers;

use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use PublicUHC\TeamspeakAuth\Entities\Authentication;
use PublicUHC\TeamspeakAuth\Entities\MinecraftAccount;
use PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount;
use TeamSpeak3;
use TeamSpeak3_Adapter_FileTransfer;
use TeamSpeak3_Adapter_ServerQuery_Exception;
use TeamSpeak3_Exception;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Server;

class DefaultTeamspeakHelper implements TeamspeakHelper {

    private $server;
    private $entityManager;
    private $groupID;
    private $mcHelper;

    public function __construct(TeamSpeak3_Node_Server $server, EntityManager $entityManager, $group_id, MinecraftHelper $mcHelper) {
        $this->server = $server;
        $this->entityManager = $entityManager;
        $this->groupID = $group_id;
        $this->mcHelper = $mcHelper;
    }

    public function verifyClient(TeamspeakAccount $tsAccount, MinecraftAccount $mcAccount) {
        $tsUUID = $tsAccount->getUUID();

        $this->setDescriptionForUUID($mcAccount->getName(), $tsUUID);

        //attempt to remove them from the group first
        try {
            $this->removeUUIDFromGroup($tsUUID, $this->groupID);
        } catch (\TeamSpeak3_Exception $ex) {}
        $this->addUUIDToGroup($tsUUID, $this->groupID);

        $authenitcation = new Authentication();
        $authenitcation->setMinecraftAccount($mcAccount)
                       ->setTeamspeakAccount($tsAccount)
                       ->setCreatedAt(new DateTime())
                       ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($authenitcation);
        $tsAccount->getCodes()->clear();
        $mcAccount->getCodes()->clear();
        $this->entityManager->flush();

        $playerIcon = $this->mcHelper->getIconForUsername($mcAccount->getName());

        $this->setIconForUUID($tsUUID, $playerIcon);
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

    /**
     * Generates a valid CRC32 for TS3 even when on 32bit PHP on Windows
     * @param $data
     * @return int crc32 of the data supplied
     */
    private function customCRC32($data)
    {
        $crc = crc32($data);
        if($crc < 0){
            $crc += 0x100000000;
        }
        return $crc;
    }

    /**
     * @param TeamSpeak3_Node_Client $client
     * @return TeamspeakAccount the updated account
     */
    public function updateLastClientUsername(TeamSpeak3_Node_Client $client)
    {
        $uuid = $this->getUUIDForClient($client);
        $account =  $this->entityManager->getRepository('PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount')->findOneBy([
            'uuid' => $uuid
        ]);

        if(null == $account) {
            $account = new TeamspeakAccount();
            $account->setCreatedAt(new DateTime())
                    ->setUUID($uuid);
        }

        $account->setName($client['client_nickname'])
                ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }

    /**
     * Get the database ID for the given UUID
     * @param $uuid string the uuid to look for
     * @return int|false the database ID if found or false otherwise
     */
    public function getClientDBId($uuid)
    {
        try {
            $infoArray = $this->getServerInstance()->clientGetNameByUid($uuid);
            return $infoArray['cldbid'];
        } catch(TeamSpeak3_Exception $ex) {
            return false;
        }
    }

    /**
     * Set the description for the client database id
     * @param $description string the description to set
     * @param $cldbid int the database id for the client
     */
    public function setDescriptionForDBId($description, $cldbid)
    {
        $this->getServerInstance()->clientModifyDb($cldbid, ['client_description' => $description]);
    }

    /**
     * Set the description for the client with the given UUID
     * @param $description string the description to set
     * @param $uuid string the client's UUID
     */
    public function setDescriptionForUUID($description, $uuid)
    {
        $cldbid = $this->getClientDBId($uuid);
        if( $cldbid !== false) {
            $this->setDescriptionForDBId($description, $cldbid);
        }
    }

    /**
     * Add the user with the given database ID to the group with the given ID
     * @param $cldbid int the database ID of the user
     * @param $groupID int the ID of the group to add to
     */
    public function addDBIdToGroup($cldbid, $groupID)
    {
        $this->getServerInstance()->serverGroupClientAdd($groupID, $cldbid);
    }

    /**
     * Add the user with the given UUID to the group with the given ID
     * @param $uuid string the UUID of the user
     * @param $groupID int the ID of the group to add to
     */
    public function addUUIDToGroup($uuid, $groupID)
    {
        $cldbid = $this->getClientDBId($uuid);
        if($cldbid !== false) {
            $this->addDBIdToGroup($cldbid, $groupID);
        }
    }

    /**
     * Remove the user with the given database ID from the group with the given ID
     * @param $cldbid int the database ID of the user
     * @param $groupID int the ID of the group to remove from
     */
    public function removeDBIdFromGroup($cldbid, $groupID)
    {
        $this->getServerInstance()->serverGroupClientDel($groupID, $cldbid);
    }

    /**
     * Remove the user with the given UUID from the group with the given ID
     * @param $uuid int the UUID of the user
     * @param $groupID int the ID of the group to remove from
     */
    public function removeUUIDFromGroup($uuid, $groupID)
    {
        $cldbid = $this->getClientDBId($uuid);
        if($cldbid !== false) {
            $this->removeDBIdFromGroup($cldbid, $groupID);
        }
    }

    /**
     * Set the icon for the user
     * @param $data string the image data to set to
     * @param $cldbid int the user's database ID
     * @return true if complete, false on error
     */
    public function setIconForDBId($cldbid, $data)
    {
        try{
            //generate our own fixed CRC32 because Windows 64bit PHP sucks
            $crc = $this->customCRC32($data);

            //length of the data
            $size = strlen($data);

            //initialize the upload of an icon, overwrite existing
            $upload = $this->getServerInstance()->transferInitUpload(rand(0x0000, 0xFFFF), 0, "/icon_" . $crc, $size, "", true);

            /** @var $transfer Teamspeak3_Adapter_FileTransfer */
            $transfer = TeamSpeak3::factory("filetransfer://" . $upload["host"] . ":" . $upload["port"]);
            $transfer->upload($upload["ftkey"], $upload["seekpos"], $data);

            //remove the permission and reassign the permission
            $this->getServerInstance()->clientPermRemove($cldbid, 'i_icon_id');

            //do some weird shit with the crc for the permission because TS3 doesn't do things like anything sane
            if ($crc > pow(2,31)) {
                $crc = $crc - 2*(pow(2,31));
            }

            //reassign the permission with the new value (the 'name' of the icon)
            $this->getServerInstance()->clientPermAssign($cldbid, 'i_icon_id', $crc);

            return true;
        }catch(Exception $ex){
            //if any exceptions were thrown we failed
            return false;
        }
    }

    /**
     * Set the icon for the user
     * @param $data string the image data to set to
     * @param $uuid string the user's UUID
     * @return true if complete, false on error
     */
    public function setIconForUUID($uuid, $data)
    {
        $cldbid = $this->getClientDBId($uuid);

        if($cldbid === false) {
            return false;
        }

        return $this->setIconForDBId($cldbid, $data);
    }
}