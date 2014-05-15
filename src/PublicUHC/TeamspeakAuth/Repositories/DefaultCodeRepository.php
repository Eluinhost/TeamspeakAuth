<?php
namespace PublicUHC\TeamspeakAuth\Repositories;


use PDO;

class DefaultCodeRepository implements CodeRepository {

    private $connection;
    private $table_name;

    public function __construct(PDO $connection, $table_name)
    {
        $this->connection = $connection;
        $this->table_name = $table_name;
    }

    public function insertCodeForUserID($userID)
    {
        $stmt = $this->connection->prepare('INSERT INTO ' . $this->table_name . '(uuid, code, created_time) VALUES (:uuid, :code, NOW()) ON DUPLICATE KEY UPDATE code=:code, created_time=NOW()');
        $stmt->bindParam('uuid', $userID, PDO::PARAM_STR);
        $stmt->bindParam('code', self::generateCode(), PDO::PARAM_STR);

        $stmt->execute();
    }

    public function generateCode($length = 10)
    {
        return substr(md5(rand()), 0, $length);
    }

    public function doesCodeMatchForUserID($code, $userID)
    {
        $stmt = $this->connection->prepare('SELECT * FROM ' . $this->table_name . ' WHERE uuid = :uuid AND code = :code');
        $stmt->bindParam('uuid', $userID, PDO::PARAM_STR);
        $stmt->bindParam('code', $code, PDO::PARAM_STR);

        $success = $stmt->execute();

        if($success === FALSE) {
            return false;
        }

        $results = $stmt->fetchAll();

        return count($results) > 0;
    }

    public function removeForUserID($userID)
    {
        $stmt = $this->connection->prepare('DELETE FROM ' . $this->table_name . ' WHERE uuid = ?');
        $stmt->bindParam('uuid', $userID, PDO::PARAM_STR);
        $stmt->execute();
    }
}