<?php
namespace PublicUHC\TeamspeakAuth\Repositories;


interface TeamspeakCodeRepository {

    /**
     * Generate a code and insert it into the database for the given UUID
     * @param $uuid string the uuid
     */
    public function insertCodeForUUID($uuid);

    /**
     * Generate a random code up to 32 characters long
     * @param int $length the length, default 10
     * @return string the code
     */
    public function generateCode($length = 10);

    /**
     * Check if the code and uuid match
     * @param $code string the code
     * @param $uuid string the uuid
     * @return boolean
     */
    public function doesCodeMatchForUUID($code, $uuid);

    /**
     * Removes the record for the given uuid
     * @param $uuid string the uuid
     */
    public function removeForUUID($uuid);
} 