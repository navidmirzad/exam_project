<?php

namespace src\Models;

use src\Database\Database;
use PDO;

require_once 'src/Database/database.php';

class Album extends Database
{
    private PDO $connect;

    public function __construct()
    {
        $this->connect = $this->connect();
    }

    public function getAll(): array
    {
        $stmt = $this->connect->query
        ("SELECT Album.AlbumId, Album.Title, Artist.ArtistId, Artist.Name AS ArtistName
            FROM Album
            JOIN Artist ON Album.ArtistId = Artist.ArtistId
            ORDER BY Album.Title");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->connect->prepare("SELECT * FROM Album WHERE AlbumId = :id");
        $stmt->execute([':id' => $id]);
        $album = $stmt->fetch();
        return $album ?: null;
    }

    public function getByTitle(string $title): array
    {
        $stmt = $this->connect->prepare("
        SELECT Album.AlbumId, Album.Title, Artist.ArtistId, Artist.Name AS ArtistName
        FROM Album
        JOIN Artist ON Album.ArtistId = Artist.ArtistId
        WHERE Album.Title LIKE :title
        ORDER BY Album.Title");
        $stmt->execute([':title' => "%$title%"]);
        return $stmt->fetchAll();
    }

    public function getAlbumAndTracks(int $albumId): ?array
    {
        $stmt = $this->connect->prepare("
            SELECT 
                Album.AlbumId, 
                Album.Title, 
                Artist.ArtistId, 
                Artist.Name AS ArtistName, 
                Track.TrackId, 
                Track.Name AS TrackName,
                MediaType.MediaTypeId,
                MediaType.Name AS MediaTypeName,
                Genre.GenreId,
                Genre.Name AS GenreName
            FROM Album
            JOIN Artist ON Album.ArtistId = Artist.ArtistId
            LEFT JOIN Track ON Album.AlbumId = Track.AlbumId
            LEFT JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            LEFT JOIN Genre ON Track.GenreId = Genre.GenreId
            WHERE Album.AlbumId = :albumId
            ORDER BY Track.TrackId
        ");
        $stmt->execute([':albumId' => $albumId]);
        return $stmt->fetchAll();
    }

    public function create(string $title, int $artistId): int
    {
        $stmt = $this->connect->prepare("INSERT INTO Album (Title, ArtistId) VALUES (:title, :artistId)");
        $stmt->execute([
            ':title' => $title,
            ':artistId' => $artistId
        ]);
        return (int)$this->connect->lastInsertId();
    }

    public function update(int $albumId, string $title, int $artistId): bool
    {
        $stmt = $this->connect->prepare("UPDATE Album SET Title = :title, ArtistId = :artistId WHERE AlbumId = :albumId");
        return $stmt->execute([
            ':title' => $title,
            ':artistId' => $artistId,
            ':albumId' => $albumId
        ]);
    }

    public function hasTracks(int $albumId): bool
    {
        $stmt = $this->connect->prepare("SELECT 1 FROM Track WHERE AlbumId = :albumId LIMIT 1");
        $stmt->execute([':albumId' => $albumId]);
        return (bool) $stmt->fetchColumn();
    }

    public function delete(int $albumId): bool
    {
        $stmt = $this->connect->prepare("DELETE FROM Album WHERE AlbumId = :albumId");
        return $stmt->execute([':albumId' => $albumId]);
    }
}
