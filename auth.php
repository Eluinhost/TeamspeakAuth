<?php
include 'AuthException.php';

const OK = 0;
const INVALID_REQUEST = 1;
const INVALID_PARAM = 2;
const INVALID_AUTH = 3;
const INVALID_TS_NICK = 4;
const DATABASE_ISSUE = 5;
const CONFIG_PROBLEM = 6;
const UNKNOWN_PROBLEM = 7;

const HTTP_OK = 200;
const HTTP_BAD_REQUEST = 400;

const PARAM_TS_NAME = 'ts_name';
const PARAM_MC_NAME = 'mc_name';
const PARAM_AUTH_CODE = 'code';

const AUTH_CODE_LENGTH = 10;
const MC_NAME_REGEX = '/^[a-zA-z0-9_]{1,16}$/';
const AUTH_CODE_REGEX = '/^[a-zA-z0-9]{10}$/';

set_exception_handler('uncaughtException');
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
    returnJSON(
        HTTP_BAD_REQUEST,
        UNKNOWN_PROBLEM,
        'Unknown error has occured processing the request'
    );
}


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
    echo json_encode(['code'=>$responseCode,'message'=>$message,'data'=>$data]);
    exit;
}

/**
 * Returns a database connection
 * @return PDO
 */
function getDatabaseConnection(){
    $config = getConfiguration();
    $conn_string = 'mysql:host='.$config->db->host.':'.$config->db->port.';dbname='.$config->db->database;

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
    $jsonContents = @file_get_contents('config.json');
    if($jsonContents === FALSE)
        throw new ConfigFileNotFoundException();
    $jsonObject = json_decode($jsonContents);
    if($jsonObject === FALSE)
        throw new ConfigFileInvalidException();
    return $jsonObject;
}

/**
 * Check the code is correct and teamspeak nick is valid, then does the verification of user in TS
 * Note: filter inputs as being valid format before passing to this method
 * @param $auth string auth code to check
 * @param $mc string MC name to check
 * @param $ts string TS nick to use
 */
function doAuth($auth,$mc,$ts){
    if(!checkAuthForMCName($auth,$mc)){
        returnJSON(HTTP_BAD_REQUEST,INVALID_AUTH,'Auth code incorrect');
    }
    if(!checkTSName($ts)){
        returnJSON(HTTP_BAD_REQUEST,INVALID_TS_NICK,'Invalid TS nickname');
    }
    //TODO MINOR connect to minotar and get icon and upload as user icon
    //TODO BLOCKER log in to ts and change user group + description
    returnJSON(HTTP_OK,OK,'Authed successfully');
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
    $stmt = $pdo->prepare('SELECT ID FROM auth_codes WHERE mc_name = :mc_name AND auth_code = :auth_code '
                         .' AND TIMESTAMPDIFF(MINUTE,created_time,NOW()) < 15 LIMIT 1');
    $stmt->bindValue(':mc_name',$mc,PDO::PARAM_STR);
    $stmt->bindValue(':auth_name',$auth,PDO::PARAM_STR);
    $stmt->execute();
    foreach($stmt as $record){
        $removeStmt = $pdo->prepare('DELETE FROM auth_codes WHERE ID = :ID');
        $removeStmt->bindValue(':ID',$record['ID'],PDO::PARAM_INT);
        $removeStmt->execute();
        return true;
    }
    return false;
}

/**
 * Checks if the name is a valid nickname inside teamspeak
 * Note: filter input as being valid format before passing to this
 * @param $name string the name to check for
 * @return bool true if a valid user exists, false otherwise
 */
function checkTSName($name){
    //TODO BLOCKER check TS for valid nickname
    return false;
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
    return preg_match(AUTH_CODE_LENGTH,$code);
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
        'Missing parameters '.implode(','.$paramNames),
        ['missing'=>$paramNames]
    );
}

//check post exists
if(!isset($_POST)){
    returnJSON(HTTP_BAD_REQUEST,INVALID_REQUEST,'Must send POST data');
}

//find missing parameters from request
$missingParams = [];
if(!isset($_POST[PARAM_TS_NAME]))
    array_push($missingParams,PARAM_TS_NAME);
if(!isset($_POST[PARAM_AUTH_CODE]))
    array_push($missingParams,PARAM_AUTH_CODE);
if(!isset($_POST[PARAM_MC_NAME]))
    array_push($missingParams,PARAM_MC_NAME);
if(count($missingParams)>0)
    returnBadParam($missingParams);

$tsName = $_POST[PARAM_TS_NAME];
$authCode = $_POST[PARAM_AUTH_CODE];
$mcName = $_POST[PARAM_MC_NAME];

//find invalid parameters in request
$invalidParams = [];
if(!isValidMCName($mcName))
    array_push($invalidParams,PARAM_MC_NAME);
if(!isValidAuthCode($authCode))
    array_push($invalidParams,PARAM_AUTH_CODE);
if(!isValidTSName($tsName))
    array_push($invalidParams,PARAM_TS_NAME);
if(count($invalidParams)>0)
    returnBadParam($invalidParams);

//attempt actual auth
doAuth($authCode,$mcName,$tsName);