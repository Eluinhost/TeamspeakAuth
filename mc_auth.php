<?php
include 'lib/Common.php';

//check get exists
if(!isset($_GET)){
    returnJSON(HTTP_BAD_REQUEST,INVALID_REQUEST,'Must send GET data');
}

//find missing parameters from request
$missingParams = [];
if(!isset($_GET[PARAM_TS_UUID]))
    array_push($missingParams,PARAM_TS_UUID);
if(!isset($_GET[PARAM_TS_AUTH_CODE]))
    array_push($missingParams,PARAM_TS_AUTH_CODE);
if(!isset($_GET[PARAM_AUTH_CODE]))
    array_push($missingParams,PARAM_AUTH_CODE);
if(!isset($_GET[PARAM_MC_NAME]))
    array_push($missingParams,PARAM_MC_NAME);
if(count($missingParams)>0)
    returnMissingParam($missingParams);

$ts_uuid = $_GET[PARAM_TS_UUID];
$ts_authCode = $_GET[PARAM_TS_AUTH_CODE];
$authCode = $_GET[PARAM_AUTH_CODE];
$mcName = $_GET[PARAM_MC_NAME];

//find invalid parameters in request
$invalidParams = [];
if(!isValidMCName($mcName))
    array_push($invalidParams,PARAM_MC_NAME);
if(!isValidAuthCode($authCode))
    array_push($invalidParams,PARAM_AUTH_CODE);
if(!isValidTSUUID($ts_uuid))
    array_push($invalidParams,PARAM_TS_UUID);
if(!isValidAuthCode($ts_authCode))
    array_push($invalidParams,PARAM_TS_AUTH_CODE);
if(count($invalidParams)>0)
    returnBadParam($invalidParams);

//attempt actual auth
doAuth($authCode,$mcName,$ts_uuid,$ts_authCode);