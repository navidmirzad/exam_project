<?php

namespace src\Models;

use PDO;
use src\Database\Database;

require_once "src/Database/database.php";

class Genre extends Database
{
    private PDO $connect;

    public function __construct()
    {
        $this->connect = $this->connect();
    }

    public function getAll()
    {
        $stmt = $this->connect->prepare("SELECT * FROM genre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
