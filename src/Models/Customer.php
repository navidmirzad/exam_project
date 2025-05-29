<?php

namespace src\Models;

use src\Database\Database;
use PDO;
require_once 'src/Database/database.php';


class Customer extends Database {

    private PDO $connect;

    public function __construct() {
        $this->connect = $this->connect();
    }

    public function getAll() {

        $stmt = $this->connect()->query('SELECT * FROM Customer');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}