<?php

namespace PublicUHC\TeamspeakAuth\Entities;

/**
 * Class User
 * Can represent either a minecraft account or a teamspeak account
 * @package PublicUHC\TeamspeakAuth\Entities
 */
class User {

    private $uuid;
    private $name;

    /**
     * @param $uuid string the unique identifier for this user
     * @param $name string the name of the user
     */
    function __construct($uuid, $name) {
        $this->uuid = $uuid;
        $this->name = $name;
    }

    function getUUID() {
        return $this->uuid;
    }

    function setName($name) {
        $this->name = $name;
    }

    function getName() {
        return $this->name;
    }
} 