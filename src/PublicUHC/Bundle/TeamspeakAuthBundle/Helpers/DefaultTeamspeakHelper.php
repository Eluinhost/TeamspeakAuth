<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Helpers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Exception;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount;
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
        //unauthenticate any accounts with the same MC name but not the same UUID
        $mcqb = $this->entityManager->createQueryBuilder();
        $mcqb->select('mcAccount')
            ->from('PublicUHCTeamspeakAuthBundle:MinecraftAccount', 'mcAccount')
            ->where(
                $mcqb->expr()->andX(
                    $mcqb->expr()->eq('mcAccount.name', ':name'),
                    $mcqb->expr()->neq('mcAccount.uuid', ':uuid')
                )
            )
            ->setParameter('name', $mcAccount->getName())
            ->setParameter('uuid', $mcAccount->getUUID());

        $accounts = $mcqb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        /** @var $account MinecraftAccount */
        foreach($accounts as $account) {
            /** @var $authentication Authentication */
            foreach($account->getAuthentications()->getValues() as $authenitcation) {
                $this->unauthenticate($authenitcation);
            }
        }

        //verify the client
        $tsUUID = $tsAccount->getUUID();

        $this->setDescriptionForUUID($mcAccount->getName(), $tsUUID);

        //attempt to remove them from the group first
        try {
            $this->removeUUIDFromGroup($tsUUID, $this->groupID);
        } catch (\TeamSpeak3_Exception $ex) {}
        $this->addUUIDToGroup($tsUUID, $this->groupID);

        $authenitcation = new Authentication();
        $authenitcation->setMinecraftAccount($mcAccount)
                       ->setTeamspeakAccount($tsAccount);

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
        $account =  $this->entityManager->getRepository('PublicUHCTeamspeakAuthBundle:TeamspeakAccount')->findOneBy([
            'uuid' => $uuid
        ]);

        if(null == $account) {
            $account = new TeamspeakAccount();
            $account->setUUID($uuid);
        }

        $account->setName($client['client_nickname']);

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

            $this->removeIconForDBId($cldbid);

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

    /**
     * Undoes the authentication supplied
     * @param Authentication $authentication the authentication to undo
     */
    public function unauthenticate(Authentication $authentication)
    {
        $tsAccount = $authentication->getTeamspeakAccount();
        $mcAccount = $authentication->getMinecraftAccount();

        $tsUUID = $tsAccount->getUUID();

        //remove their description
        $this->setDescriptionForUUID('', $tsUUID);

        //attempt to remove them from the group
        try {
            $this->removeUUIDFromGroup($tsUUID, $this->groupID);
        } catch (TeamSpeak3_Exception $ex) {}

        //remove the permission
        $this->getServerInstance()->clientPermRemove($this->getClientDBId($tsUUID), 'i_icon_id');

        $tsAccount->getAuthentications()->removeElement($authentication);
        $mcAccount->getAuthentications()->removeElement($authentication);

        $this->entityManager->remove($authentication);
        $this->entityManager->remove($mcAccount);
        $this->entityManager->persist($mcAccount);
        $this->entityManager->persist($tsAccount);

        $this->entityManager->flush();
    }

    /**
     * Check if the teamspeak user with the given UUID is online
     * @param $uuid string the UUID to check
     * @return boolean
     */
    public function isUUIDOnline($uuid)
    {
        try {
            $this->getServerInstance()->clientGetByUid($uuid);
            return true;
        } catch( TeamSpeak3_Exception $ex ) {
            return false;
        }
    }

    /**
     * @param $groupID int the group ID to look for
     * @return int[] a list of DBIDs in the given group
     */
    public function getDBIdsForGroupID($groupID)
    {
        try {
            $returnArray = [];
            $ids = $this->getServerInstance()->serverGroupClientList($groupID);
            foreach($ids as $id) {
                array_push($returnArray, $id['cldbid']);
            }
            return $returnArray;
        } catch (TeamSpeak3_Exception $ex) {
            return [];
        }
    }

    /**
     * Remove the client icon for the user
     * @param $cldbid int the dbid of the user
     */
    public function removeIconForDBId($cldbid)
    {
        $this->getServerInstance()->clientPermRemove($cldbid, 'i_icon_id');
    }

    /**
     * Remove the client icon for the user
     * @param $uuid string the uuid of the user
     */
    public function removeIconForUUID($uuid)
    {
        $cldbid = $this->getClientDBId($uuid);
        if($cldbid !== false) {
            $this->removeIconForDBId($cldbid);
        }
    }

    /**
     * @param $cldbid int the client DBId
     * @return String uuid
     */
    public function getUUIDForDBId($cldbid)
    {
        return ''.$this->getServerInstance()->clientInfoDb($cldbid)['client_unique_identifier'];
    }

    /**
     * @return int[] a list of all DBIds
     */
    public function getAllDBIds()
    {
        try {
            $returnArray = [];
            $ids = $this->server->clientListDb(0, 2100000);
            foreach($ids as $id) {
                array_push($returnArray, $id['cldbid']);
            }
            return $returnArray;
        } catch (TeamSpeak3_Exception $ex) {
            return [];
        }
    }
}