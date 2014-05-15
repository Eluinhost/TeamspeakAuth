<?php

namespace PublicUHC\TeamspeakAuth\Helpers;

use Exception;
use TeamSpeak3;
use TeamSpeak3_Adapter_FileTransfer;
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

    public function setClientIcon(TeamSpeak3_Node_Client $client, $icon)
    {
        try{
            //generate our own fixed CRC32 because Windows 64bit PHP sucks
            $crc = $this->customCRC32($icon);

            //length of the data
            $size = strlen($icon);

            //initialize the upload of an icon, overwrite existing
            $upload = $this->getServerInstance()->transferInitUpload(rand(0x0000, 0xFFFF), 0, "/icon_" . $crc, $size, "", true);

            /** @var $transfer Teamspeak3_Adapter_FileTransfer */
            $transfer = TeamSpeak3::factory("filetransfer://" . $upload["host"] . ":" . $upload["port"]);
            $transfer->upload($upload["ftkey"], $upload["seekpos"], $icon);

            //remove the permission and reassign the permission
            $client->permRemove('i_icon_id');

            //do some weird shit with the crc for the permission because TS3 doesn't do things like anything sane
            if ($crc > pow(2,31)) {
                $crc = $crc - 2*(pow(2,31));
            }

            //reassign the permission with the new value (the 'name' of the icon)
            $client->permAssignByName('i_icon_id',$crc);

            return true;
        }catch(Exception $ex){
            //if any exceptions were thrown we failed
            return false;
        }
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

    public function setClientDescription(TeamSpeak3_Node_Client $client, $description)
    {
        $client->modifyDb(['client_description' => $description]);
    }

    public function getClientByUUID($uuid) {
        return $this->server->clientGetByUid($uuid);
    }
}