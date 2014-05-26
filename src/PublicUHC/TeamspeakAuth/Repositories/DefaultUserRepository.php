<?php
namespace PublicUHC\TeamspeakAuth\Repositories;


use PDO;
use PublicUHC\TeamspeakAuth\Entities\User;

class DefaultUserRepository implements UserRepository {

    private $connection;
    private $table_name;

    public function __construct(PDO $connection, $table_name)
    {
        $this->connection = $connection;
        $this->table_name = $table_name;
    }

    /**
     * Get the user for the given UUID
     * @param $uuid string the uuid to search for
     * @return User|false the user if found, false if not
     */
    function getUserForUUID($uuid)
    {
        $stmt = $this->connection->prepare(
            'SELECT uuid, name FROM ' . $this->table_name . ' WHERE BINARY uuid = :uuid');
        $stmt->bindParam('uuid', $uuid, PDO::PARAM_STR);

        $users = $stmt->fetchAll();
        if(count($users) == 0) {
            return false;
        }
        $userArray = $users[0];

        return new User($userArray['uuid'], $userArray['name']);
    }

    /**
     * Set the last seen name for the given UUID
     * @param $uuid string the uuid to update/create
     * @param $username string the username to update to
     */
    function setLastNameForUUID($uuid, $username)
    {
        $stmt = $this->connection->prepare('INSERT INTO ' . $this->table_name . '(uuid, name, updated_time) VALUES (:uuid, :name, NOW()) ON DUPLICATE KEY UPDATE name=:name, updated_time=NOW()');
        $stmt->bindParam('uuid', $uuid);
        $stmt->bindParam('name', $username);

        $stmt->execute();
    }
}