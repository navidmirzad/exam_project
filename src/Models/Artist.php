<?php

namespace src\Models;

use src\Database\Database;
use PDO;

require_once "src/Database/database.php";


class Artist extends Database
{
    private PDO $connect;

    public function __construct()
    {
        $this->connect = $this->connect();
    }

    public function getAll(): array
    { 
        $stmt = $this->connect->query("SELECT * FROM Artist ORDER BY Name");
        return $stmt->fetchAll();
    }

    public function getByName(string $name): ?array 
    {
        $stmt = $this->connect->prepare("SELECT * FROM Artist WHERE Name = :name");
        $stmt->execute([':name' => $name]);
        $artist = $stmt->fetch();
        return $artist ?: null;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->connect->prepare("SELECT * FROM Artist WHERE ArtistId = :id");
        $stmt->execute([':id' => $id]);
        $artist = $stmt->fetch();
        return $artist ?: null;
    }

    // Create artist
    public function create(string $name): int
    {
        $stmt = $this->connect->prepare("INSERT INTO Artist (Name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        return (int)$this->connect->lastInsertId();
    }

    // Check if artist has albums
    public function hasAlbums(int $artistId): bool
    {
        $stmt = $this->connect->prepare("SELECT 1 FROM Album WHERE ArtistId = :artistId LIMIT 1");
        $stmt->execute([':artistId' => $artistId]);
        return (bool) $stmt->fetchColumn();
    }

    // Delete artist by ID
    public function delete(int $id): bool
    {
        $stmt = $this->connect->prepare("DELETE FROM Artist WHERE ArtistId = :id");
        return $stmt->execute([':id' => $id]);
    }
}
