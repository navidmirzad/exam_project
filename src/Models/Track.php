<?php

namespace src\Models;

use PDO;
use src\Database\Database;

require_once "src/Database/database.php";

class Track extends Database
{
    private PDO $connect;

    public function __construct()
    {
        $this->connect = $this->connect();
    }

    public function search(string $search)
    {
        $stmt = $this->connect->prepare("
            SELECT track.TrackId, track.Name, mediatype.MediaTypeId, mediatype.Name AS MediaTypeName, genre.GenreId, genre.Name AS GenreName
            FROM track
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            JOIN genre ON track.GenreId = genre.GenreId
            WHERE track.Name LIKE :search
        ");
        $stmt->execute([':search' => "%$search%"]);
        return $stmt->fetchAll();
    }

}