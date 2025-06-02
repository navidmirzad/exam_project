<?php

namespace src\Database;

use DBCredentials;
use PDO;

require_once 'DBCredentials.php';

class Database extends DBCredentials
{
    protected function connect(): PDO
    {
        // (DSN = Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        $options = [
            // Errors will throw exceptions
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            // Results will be returned in associative arrays
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // create and return PDO object // PHP Data Object
        $pdo = new PDO($dsn, $this->user, $this->password, $options);
        return $pdo;
    }
}

?>
