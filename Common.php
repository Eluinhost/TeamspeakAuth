<?php

require_once 'vendor/autoload.php';

const OK = 0;
const INVALID_REQUEST = 1;
const INVALID_PARAM = 2;
const TEAMSPEAK_ISSUE = 3;
const INVALID_TS_UUID = 4;
const DATABASE_ISSUE = 5;
const CONFIG_PROBLEM = 6;
const UNKNOWN_PROBLEM = 7;
const INVALID_MC_AUTH = 8;
const INVALID_TS_AUTH = 9;
const INVALID_TS_NAME = 10;
const MISSING_PARAM = 11;

const HTTP_OK = 200;
const HTTP_BAD_REQUEST = 400;

const PARAM_TS_UUID = 'ts_uuid';
const PARAM_TS_AUTH_CODE = 'ts_code';
const PARAM_MC_NAME = 'mc_name';
const PARAM_AUTH_CODE = 'code';
const PARAM_TS_NAME = 'ts_name';

const AUTH_CODE_LENGTH = 10;
const MC_NAME_REGEX = '/^[a-zA-z0-9_]{1,16}$/';
const AUTH_CODE_REGEX = '/^[a-fA-F0-9]{10}$/';

/**
* Catches all uncaught errors
* @param $e
*/
function uncaughtException($e) {
    if($e instanceof PDOException)
        returnJSON(
            HTTP_BAD_REQUEST,
            DATABASE_ISSUE,
            'Error accessing the database'
        );
    if($e instanceof ConfigFileNotFoundException || $e instanceof ConfigFileInvalidException)
        returnJSON(
            HTTP_BAD_REQUEST,
            CONFIG_PROBLEM,
            'Config file missing or invalid'
        );
    if($e instanceof TeamSpeak3_Exception)
        returnJSON(
            HTTP_BAD_REQUEST,
            TEAMSPEAK_ISSUE,
            'There was an error contacting teamspeak '.$e->getMessage()
        );
    returnJSON(
        HTTP_BAD_REQUEST,
        UNKNOWN_PROBLEM,
        'Unknown error has occured processing the request'
    );
}
set_exception_handler('uncaughtException');

/**
* Send json back and quit
* @param $code int http resonse code
* @param $responseCode int script response code
* @param $message string script response message
* @param null|mixed $data data to be added to data tag in json
*/
function returnJSON($code,$responseCode,$message,$data=null){
    header('Content-type: application/json');
    http_response_code($code);
    echo json_encode(['code'=>$responseCode,'message'=>$message,'data'=>$data],JSON_FORCE_OBJECT);
    exit;
}

/**
* Returns a database connection
* @return PDO
*/
function getDatabaseConnection(){
    global $config;
    $conn_string = 'mysql:host='.$config->db->host.';port='.$config->db->port.';dbname='.$config->db->database;

    $dbh = new PDO($conn_string, $config->db->username, $config->db->password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

/**
* Returns the configuration object
* @throws ConfigFileNotFoundException
* @throws ConfigFileInvalidException
* @return object parsed json
*/
function getConfiguration(){
    $jsonContents = @file_get_contents('config/config.json');
    if($jsonContents === FALSE)
        throw new ConfigFileNotFoundException();
    $jsonObject = json_decode($jsonContents);
    if($jsonObject === FALSE)
        throw new ConfigFileInvalidException();
    return $jsonObject;
}

/**
* Generates a valid crc32 even on 32bit PHP
* @param $data
* @return int crc32 of the data supplied
*/
function crcKw($data){
    $crc = crc32($data);
    if($crc < 0){
        $crc += 0x100000000;
    }
    return $crc;
}

/**
* Uploads the icon to the server
 * @param $crc
* @param $data
* @param $server
*/
function uploadIcon($crc,$data,TeamSpeak3_Node_Server $server){
    $size = strlen($data);
    $upload   = $server->transferInitUpload(rand(0x0000, 0xFFFF), 0, "/icon_" . $crc, $size,"",true);
    /** @var $transfer Teamspeak3_Adapter_FileTransfer */
    $transfer = TeamSpeak3::factory("filetransfer://" . $upload["host"] . ":" . $upload["port"]);
    $transfer->upload($upload["ftkey"], $upload["seekpos"], $data);
}

/**
* Check the code is correct and teamspeak nick is valid, then does the verification of user in TS
* Note: filter inputs as being valid format before passing to this method
* @param $auth string auth code to check
* @param $mc string MC name to check
* @param $ts_uuid string TS uuid to use
* @param $ts_authCode string ts auth code to check
*/
function doAuth($auth,$mc,$ts_uuid,$ts_authCode){
    global $config;
    if(!checkAuthForMCName($auth,$mc))
    returnJSON(HTTP_BAD_REQUEST,INVALID_MC_AUTH,'Auth code incorrect');
    if(!checkAuthForTSUUID($ts_authCode,$ts_uuid))
    returnJSON(HTTP_BAD_REQUEST,INVALID_TS_AUTH,'Auth code incorrect');
    $server = getServerInstance();
    $client = getTSClient($ts_uuid,$server);
    if($client === false){
        returnJSON(HTTP_BAD_REQUEST,INVALID_TS_UUID,'Invalid TS UUID');
    }
    try{
        $client->remServerGroup($config->ts->group_id);
    }catch (Exception $ex){}
    $client->addServerGroup($config->ts->group_id);
    $client->modifyDb(['client_description'=>$mc]);
    deleteAuthCodes($mc,$ts_uuid);
    $data = 'success';
    try{
        $data = file_get_contents('https://minotar.net/helm/'.$mc.'/16.png');
        $crc = crcKw($data);
        try{
            uploadIcon($crc,$data,$server);
        }catch (Exception $ex){}
        $client->permRemove('i_icon_id');
        if ($crc > pow(2,31)) {
            $crc = $crc - 2*(pow(2,31));
        }
        $client->permAssignByName('i_icon_id',$crc);
    }catch(Exception $ex){
        $data = $ex->getTraceAsString().'<br/>'.$ex->getMessage();
    }
    returnJSON(HTTP_OK,OK,'Authed successfully',$data);
}

/**
* Deletes the codes for the given mc name and ts uuid from the database
* @param $mc
* @param $ts
*/
function deleteAuthCodes($mc,$ts){
    $pdo = getDatabaseConnection();
    $removeStmt = $pdo->prepare('DELETE FROM auth_codes WHERE mc_name = :mc_name');
    $removeStmt->bindValue(':mc_name',$mc,PDO::PARAM_STR);
    $removeStmt->execute();
    $removeStmt = $pdo->prepare('DELETE FROM ts_uuids WHERE ts_uuid = :ts_uuid');
    $removeStmt->bindValue(':ts_uuid',$ts,PDO::PARAM_STR);
    $removeStmt->execute();
}

/**
* Checks the database to see if the auth code is correct for the MC name, removes auth code from database if correct.
* Note: filter inputs as being valid format before passing to this method
* @param $auth string the auth code
* @param $mc string the MC name
* @return bool whether it was the correct code or not
*/
function checkAuthForMCName($auth,$mc){
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT ID FROM auth_codes WHERE BINARY mc_name = :mc_name AND BINARY auth_code = :auth_code '
    .' AND TIMESTAMPDIFF(MINUTE,created_time,NOW()) < 15 LIMIT 1');
    $stmt->bindValue(':mc_name',$mc,PDO::PARAM_STR);
    $stmt->bindValue(':auth_code',$auth,PDO::PARAM_STR);
    $stmt->execute();
    foreach($stmt as $record){
        return true;
    }
    return false;
}

/**
* Checks the database to see if the auth code is correct for the TS UUID
* Note: filter inputs as being valid format before passing to this method
* @param $auth string the auth code
* @param $uuid string the TS UUID
* @return bool whether it was the correct code or not
*/
function checkAuthForTSUUID($auth,$uuid){
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare('SELECT ID FROM ts_uuids WHERE BINARY ts_uuid = :ts_uuid AND BINARY auth_code = :auth_code '
    .' AND TIMESTAMPDIFF(MINUTE,created_time,NOW()) < 15 LIMIT 1');
    $stmt->bindValue(':ts_uuid',$uuid,PDO::PARAM_STR);
    $stmt->bindValue(':auth_code',$auth,PDO::PARAM_STR);
    $stmt->execute();
    foreach($stmt as $record){
        return true;
    }
    return false;
}

/**
* Gets the client if found or false if not found
* @param $ts_uuid string the UUID to check for
* @param TeamSpeak3_Node_Server $server the server to search
* @return Teamspeak3_Node_Client|bool false if a valid user exists, false otherwise
*/
function getTSClient($ts_uuid,TeamSpeak3_Node_Server $server){
    try{
        return $server->clientGetByUid($ts_uuid);
    }catch (TeamSpeak3_Adapter_ServerQuery_Exception $ignored){}
    return false;
}

/**
 * Gets the client if found or false if not found
 * @param $name string the username to check for
 * @param TeamSpeak3_Node_Server $server the server to search
 * @return Teamspeak3_Node_Client|bool false if a valid user exists, false otherwise
 */
function getTSClientByName($name,TeamSpeak3_Node_Server $server){
    try{
        return $server->clientGetByName($name);
    }catch (TeamSpeak3_Adapter_ServerQuery_Exception $ignored){}
    return false;
}

/**
* Gets the TS server instance
* @return TeamSpeak3_Node_Server
*/
function getServerInstance(){
    global $config;
    return TeamSpeak3::factory("serverquery://{$config->ts->username}:{$config->ts->password}@{$config->ts->host}:{$config->ts->query_port}/?server_port={$config->ts->port}");
}

/**
* Basic format verification for teamspeak UUID
* @param $name string input
* @return bool true if valid format, false otherwise
*/
function isValidTSUUID($name){
    return strlen($name)>0&&strlen($name)<=128;
}

/**
 * Basic format verification for teamspeak name
 * @param $name string input
 * @return bool true if valid format, false otherwise
 */
function isValidTSName($name){
    return strlen($name)>0&&strlen($name)<=30;
}

/**
* Basic format verification for MC name
* @param $name string input
* @return bool true if valid format, false otherwise
*/
function isValidMCName($name){
    return preg_match(MC_NAME_REGEX,$name);
}

/**
* Basic format verification for Auth code
* @param $code string input
* @return bool true if valid, false otherwise
*/
function isValidAuthCode($code){
    return preg_match(AUTH_CODE_REGEX,$code);
}

/**
* Sends json response back for invalid parameters
* @param $paramNames array array of invalid params
*/
function returnBadParam($paramNames){
    if(!is_array($paramNames)){
        $paramNames = [$paramNames];
    }
    returnJSON(
        HTTP_BAD_REQUEST,
        INVALID_PARAM,
        'Invalid parameters '.implode(','.$paramNames),
        ['missing'=>$paramNames]
    );
}


/**
 * Sends json response back for invalid parameters
 * @param $paramNames array array of invalid params
 */
function returnMissingParam($paramNames){
    if(!is_array($paramNames)){
        $paramNames = [$paramNames];
    }
    returnJSON(
        HTTP_BAD_REQUEST,
        MISSING_PARAM,
        'Missing parameters '.implode(','.$paramNames),
        ['missing'=>$paramNames]
    );
}

/**
 * Requests the ingame PM for codes
 * @param $name string the user to message
 * @return string UUID of user
 */
function requestTeamspeakCodes($name){
    $server = getServerInstance();
    $client = getTSClientByName($name,$server);
    if($client === FALSE)
        returnJSON(HTTP_BAD_REQUEST,INVALID_TS_NAME,'Invalid Teamspeak Nickname');
    $code = generateCode();
    $UUID = $client->infoDb()['client_unique_identifier'];
    $dbo = getDatabaseConnection();
    $stmt = $dbo->prepare('INSERT INTO ts_uuids(ts_uuid,auth_code) VALUES (:uuid,:code) '.
                          'ON DUPLICATE KEY UPDATE auth_code = :code2,created_time=NOW()');
    $stmt->bindValue(':uuid',$UUID,PDO::PARAM_STR);
    $stmt->bindValue(':code',$code,PDO::PARAM_STR);
    $stmt->bindValue(':code2',$code,PDO::PARAM_STR);
    $stmt->execute();
    $client->message("[Verification Codes] UUID: '{$UUID}' AUTH CODE: '{$code}'. This code work for the next 15 minutes");
    return $UUID;
}

/**
 * Generates a random 10 character code
 * @return string
 */
function generateCode(){
    return substr(md5(rand()), 0, 10);
}

$config = getConfiguration();
