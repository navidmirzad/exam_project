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
        $stmt = $this->connect->prepare("SELECT * FROM album WHERE AlbumId = :id");
        $stmt->execute([':id' => $id]);
        $album = $stmt->fetch();
        return $album ?: null;
    }

    public function getByTitle(string $title): array
    {
        $stmt = $this->connect->prepare("
        SELECT album.AlbumId, album.Title, artist.ArtistId, artist.Name AS ArtistName
        FROM album
        JOIN artist ON album.ArtistId = artist.ArtistId
        WHERE album.Title LIKE :title
        ORDER BY album.Title");
        $stmt->execute([':title' => "%$title%"]);
        return $stmt->fetchAll();
    }

    public function getAlbumAndTracks(int $albumId): ?array
    {
        $stmt = $this->connect->prepare("
            SELECT 
                album.AlbumId, 
                album.Title, 
                artist.ArtistId, 
                artist.Name AS ArtistName, 
                track.TrackId, 
                track.Name AS TrackName,
                mediatype.MediaTypeId,
                mediatype.Name AS MediaTypeName,
                genre.GenreId,
                genre.Name AS GenreName
            FROM album
            JOIN artist ON album.ArtistId = artist.ArtistId
            LEFT JOIN track ON album.AlbumId = track.AlbumId
            LEFT JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            LEFT JOIN genre ON track.GenreId = genre.GenreId
            WHERE album.AlbumId = :albumId
            ORDER BY track.TrackId
        ");
        $stmt->execute([':albumId' => $albumId]);
        return $stmt->fetchAll();
    }

    public function create(string $title, int $artistId): int
    {
        $stmt = $this->connect->prepare("INSERT INTO album (Title, ArtistId) VALUES (:title, :artistId)");
        $stmt->execute([
            ':title' => $title,
            ':artistId' => $artistId
        ]);
        return (int)$this->connect->lastInsertId();
    }

    public function update(int $albumId, string $title, int $artistId): bool
    {
        $stmt = $this->connect->prepare("UPDATE album SET Title = :title, ArtistId = :artistId WHERE AlbumId = :albumId");
        return $stmt->execute([
            ':title' => $title,
            ':artistId' => $artistId,
            ':albumId' => $albumId
        ]);
    }

    public function hasTracks(int $albumId): bool
    {
        $stmt = $this->connect->prepare("SELECT 1 FROM track WHERE AlbumId = :albumId LIMIT 1");
        $stmt->execute([':albumId' => $albumId]);
        return (bool) $stmt->fetchColumn();
    }

    public function delete(int $albumId): bool
    {
        $stmt = $this->connect->prepare("DELETE FROM album WHERE AlbumId = :albumId");
        return $stmt->execute([':albumId' => $albumId]);
    }
}
