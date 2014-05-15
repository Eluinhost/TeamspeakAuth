<?php
namespace PublicUHC\TeamspeakAuth\Repositories;


interface CodeRepository {

    /**
     * Generate a code and insert it into the database for the given userID
     * @param $userID string an identifier for the user
     * @return $code string the generated code
     */
    public function insertCodeForUserID($userID);

    /**
     * Generate a random code up to 32 characters long
     * @param int $length the length, default 10
     * @return string the code
     */
    public function generateCode($length = 10);

    /**
     * Check if the code and uuid match
     * @param $code string the code
     * @param $userID string an identifier for the user
     * @return boolean
     */
    public function doesCodeMatchForUserID($code, $userID);

    /**
     * Removes the record for the given user ID
     * @param $userID string an identifier for the user
     */
    public function removeForUserID($userID);
} 