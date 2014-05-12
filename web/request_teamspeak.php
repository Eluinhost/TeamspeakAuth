<?php
include './../lib/Common.php';

if(!isset($_GET) || !isset($_GET['ts_name'])){
    returnJSON(HTTP_BAD_REQUEST,INVALID_REQUEST,'Must send GET data');
}

//find missing parameters from request
$missingParams = [];
if(!isset($_GET[PARAM_TS_NAME]))
    array_push($missingParams,PARAM_TS_NAME);
if(count($missingParams)>0)
    returnMissingParam($missingParams);

$ts_name = $_GET[PARAM_TS_NAME];

//find invalid parameters in request
$invalidParams = [];
if(!isValidTSName($ts_name))
    array_push($invalidParams,PARAM_TS_NAME);
if(count($invalidParams)>0)
    returnBadParam($invalidParams);

$UUID = ''.requestTeamspeakCodes($_GET['ts_name']);
returnJSON(HTTP_OK,OK,'Requested successfully',$UUID);