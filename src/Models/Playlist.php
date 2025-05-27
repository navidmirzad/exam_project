<?php

namespace src\Models;

use PDO;
use src\Database\Database;

require_once "src/Database/database.php";

class Playlist extends Database
{
    private PDO $connect;

    public function __construct()
    {
        $this->connect = $this->connect();
    }

    public function getAll()
    {
        $stmt = $this->connect->prepare("SELECT * FROM Playlist");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(string $name)
    {
        $stmt = $this->connect->prepare("SELECT * FROM Playlist WHERE Name LIKE :name");
        $stmt->execute([':name' => "%$name%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $stmt = $this->connect->prepare("SELECT * FROM Playlist WHERE PlaylistId = :id");
        $stmt->execute([':id' => $id]);
        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$playlist) {
            return null;
        }

        // Get tracks for this playlist
        $stmt = $this->connect->prepare("
            SELECT 
                t.TrackId, t.Name, t.AlbumId, t.MediaTypeId, t.GenreId, t.Composer, t.Milliseconds, t.Bytes, t.UnitPrice
            FROM PlaylistTrack pt
            JOIN Track t ON pt.TrackId = t.TrackId
            WHERE pt.PlaylistId = :id
        ");
        $stmt->execute([':id' => $id]);
        $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $playlist['tracks'] = $tracks;
        return $playlist;
    }

    public function create(string $name)
    {
        $stmt = $this->connect->prepare("INSERT INTO Playlist (Name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        return $this->connect->lastInsertId();
    }

    public function addTrack(int $playlist_id, int $track_id)
    {
        // Prevent duplicate assignment
        $stmt = $this->connect->prepare("SELECT 1 FROM PlaylistTrack WHERE PlaylistId = :playlist_id AND TrackId = :track_id");
        $stmt->execute([':playlist_id' => $playlist_id, ':track_id' => $track_id]);
        if ($stmt->fetch()) {
            return false; // Already assigned
        }
        $stmt = $this->connect->prepare("INSERT INTO PlaylistTrack (PlaylistId, TrackId) VALUES (:playlist_id, :track_id)");
        return $stmt->execute([':playlist_id' => $playlist_id, ':track_id' => $track_id]);
    }

    public function removeTrack(int $playlist_id, int $track_id)
    {
        $stmt = $this->connect->prepare("DELETE FROM PlaylistTrack WHERE PlaylistId = :playlist_id AND TrackId = :track_id");
        return $stmt->execute([':playlist_id' => $playlist_id, ':track_id' => $track_id]);
    }

    public function delete(int $playlist_id)
    {
        // Check if playlist has any tracks
        $stmt = $this->connect->prepare("SELECT 1 FROM PlaylistTrack WHERE PlaylistId = :playlist_id LIMIT 1");
        $stmt->execute([':playlist_id' => $playlist_id]);
        if ($stmt->fetch()) {
            // Playlist has tracks, do not delete
            return false;
        }
        // Safe to delete
        $stmt = $this->connect->prepare("DELETE FROM Playlist WHERE PlaylistId = :playlist_id");
        return $stmt->execute([':playlist_id' => $playlist_id]);
    }
}
