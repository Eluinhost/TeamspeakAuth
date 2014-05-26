<?php

namespace PublicUHC\TeamspeakAuth\Repositories;


use PublicUHC\TeamspeakAuth\Entities\User;

interface UserRepository {

    /**
     * Get the user for the given UUID
     * @param $uuid string the uuid to search for
     * @return User|false the user if found, false if not
     */
    function getUserForUUID($uuid);

    /**
     * Set the last seen name for the given UUID
     * @param $uuid string the uuid to update/create
     * @param $username string the username to update to
     */
    function setLastNameForUUID($uuid, $username);
} 