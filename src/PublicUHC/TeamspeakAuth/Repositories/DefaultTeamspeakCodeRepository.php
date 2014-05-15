<?php
namespace PublicUHC\TeamspeakAuth\Repositories;


use PDO;

class DefaultTeamspeakCodeRepository implements TeamspeakCodeRepository {

    private $connection;
    private $ts_table;
    private $mc_table;

    public function __construct(PDO $connection, $ts_table, $mc_table)
    {
        $this->connection = $connection;
        $this->ts_table = $ts_table;
        $this->mc_table = $mc_table;
    }

    public function insertCodeForUUID($uuid)
    {
        $stmt = $this->connection->prepare('INSERT INTO ' . $this->ts_table . '(uuid, code, created_time) VALUES (:uuid, :code, NOW()) ON DUPLICATE KEY UPDATE code=:code, created_time=NOW()');
        $stmt->bindParam('uuid', $uuid, PDO::PARAM_STR);
        $stmt->bindParam('code', generateCode(), PDO::PARAM_STR);

        $stmt->execute();
    }

    public function generateCode($length = 10)
    {
        return substr(md5(rand()), 0, $length);
    }

    public function doesCodeMatchForUUID($code, $uuid)
    {
        $stmt = $this->connection->prepare('SELECT * FROM ' . $this->ts_table . ' WHERE uuid = :uuid AND code = :code');
        $stmt->bindParam('uuid', $uuid, PDO::PARAM_STR);
        $stmt->bindParam('code', $code, PDO::PARAM_STR);

        $success = $stmt->execute();

        if($success === FALSE) {
            return false;
        }

        $results = $stmt->fetchAll();

        return count($results) > 0;
    }

    public function removeForUUID($uuid)
    {
        $stmt = $this->connection->prepare('DELETE FROM ' . $this->ts_table . ' WHERE uuid = ?');
        $stmt->bindParam('uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
    }
}