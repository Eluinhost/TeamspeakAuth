<?php
namespace PublicUHC\TeamspeakAuth\Repositories;


use PDO;

class DefaultCodeRepository implements CodeRepository {

    private $connection;
    private $table_name;
    private $mintuesToLast;

    public function __construct(PDO $connection, $table_name, $minutesToLast)
    {
        $this->connection = $connection;
        $this->table_name = $table_name;
        $this->mintuesToLast = $minutesToLast;
    }

    public function insertCodeForUserID($userID)
    {
        $stmt = $this->connection->prepare('INSERT INTO ' . $this->table_name . '(uuid, code, created_time) VALUES (:uuid, :code, NOW()) ON DUPLICATE KEY UPDATE code=:code, created_time=NOW()');
        $stmt->bindParam('uuid', $userID, PDO::PARAM_STR);
        $code = self::generateCode();
        $stmt->bindParam('code', $code, PDO::PARAM_STR);

        $stmt->execute();
        return $code;
    }

    public function generateCode($length = 10)
    {
        return substr(md5(rand()), 0, $length);
    }

    public function doesCodeMatchForUserID($code, $userID)
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM ' . $this->table_name . ' WHERE BINARY uuid = :uuid AND BINARY code = :code AND TIMESTAMPDIFF(MINUTE,created_time,NOW()) < :minutes LIMIT 1'
        );
        $stmt->bindParam('uuid', $userID, PDO::PARAM_STR);
        $stmt->bindParam('code', $code, PDO::PARAM_STR);
        $stmt->bindParam('minutes', $this->mintuesToLast, PDO::PARAM_INT);

        $success = $stmt->execute();

        if($success === FALSE) {
            return false;
        }

        $results = $stmt->fetchAll();

        return count($results) > 0;
    }

    public function removeForUserID($userID)
    {
        $stmt = $this->connection->prepare('DELETE FROM ' . $this->table_name . ' WHERE uuid = :uuid');
        $stmt->bindParam('uuid', $userID, PDO::PARAM_STR);
        $stmt->execute();
    }
}