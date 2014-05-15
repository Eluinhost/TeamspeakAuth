<?php
namespace PublicUHC\TeamspeakAuth;


use PDO;

class TeamspeakCodeRepository {

    private $connection;
    private $ts_table;
    private $mc_table;

    public function __construct(PDO $connection, $ts_table, $mc_table) {
        $this->connection = $connection;
        $this->ts_table = $ts_table;
        $this->mc_table = $mc_table;
    }

    public function insertCodeForUUID($uuid, $code) {
        $stmt = $this->connection->prepare('INSERT INTO ' . $this->ts_table . '(uuid, code, created_time) VALUES (:uuid, :code, NOW()) ON DUPLICATE KEY UPDATE code=:code, created_time=NOW()');
        $stmt->bindParam('uuid', $uuid, PDO::PARAM_STR);
        $stmt->bindParam('code', $code, PDO::PARAM_STR);

        $stmt->execute();
    }
} 