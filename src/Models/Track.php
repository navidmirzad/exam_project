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
            SELECT Track.TrackId, Track.Name, MediaType.MediaTypeId, MediaType.Name AS MediaTypeName, Genre.GenreId, Genre.Name AS GenreName
            FROM Track
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            JOIN Genre ON Track.GenreId = Genre.GenreId
            WHERE Track.Name LIKE :search
        ");
        $stmt->execute([':search' => "%$search%"]);
        return $stmt->fetchAll();
    }

    public function getById(int $id)
    {
        $stmt = $this->connect->prepare("
            SELECT 
                Track.TrackId, 
                Track.Name, 
                Track.AlbumId, 
                Track.MediaTypeId, 
                MediaType.Name AS MediaTypeName, 
                Track.GenreId, 
                Genre.Name AS GenreName, 
                Track.Composer, 
                Track.Milliseconds, 
                Track.Bytes, 
                Track.UnitPrice
            FROM Track
            JOIN MediaType ON Track.MediaTypeId = MediaType.MediaTypeId
            JOIN Genre ON Track.GenreId = Genre.GenreId
            WHERE Track.TrackId = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByComposer(string $composer)
    {
        $stmt = $this->connect->prepare("
            SELECT * FROM Track
            WHERE Composer LIKE :composer
        ");
        $stmt->execute([':composer' => "%$composer%"]);
        return $stmt->fetchAll();
    }

    public function create(array $data)
    {
        $stmt = $this->connect->prepare("
            INSERT INTO Track 
                (Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice)
            VALUES 
                (:name, :album_id, :media_type_id, :genre_id, :composer, :milliseconds, :bytes, :unit_price)
        ");
        $stmt->execute([
            ':name' => $data['name'],
            ':album_id' => $data['album_id'],
            ':media_type_id' => $data['media_type_id'],
            ':genre_id' => $data['genre_id'],
            ':composer' => $data['composer'],
            ':milliseconds' => $data['milliseconds'],
            ':bytes' => $data['bytes'],
            ':unit_price' => $data['unit_price']
        ]);
        return $this->connect->lastInsertId();
    }

    public function update(int $track_id, array $data)
    {
        $fields = [];
        $params = [':track_id' => $track_id];
        $map = [
            'name' => 'Name',
            'album_id' => 'AlbumId',
            'media_type_id' => 'MediaTypeId',
            'genre_id' => 'GenreId',
            'composer' => 'Composer',
            'milliseconds' => 'Milliseconds',
            'bytes' => 'Bytes',
            'unit_price' => 'UnitPrice'
        ];
        foreach ($map as $key => $column) {
            if (isset($data[$key])) {
                $fields[] = "$column = :$key";
                $params[":$key"] = $data[$key];
            }
        }
        if (!$fields) {
            return false;
        }
        $sql = "UPDATE Track SET " . implode(', ', $fields) . " WHERE TrackId = :track_id";
        $stmt = $this->connect->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $track_id)
    {
        // Check if the track exists in any playlist
        $stmt = $this->connect->prepare("SELECT 1 FROM PlaylistTrack WHERE TrackId = :track_id LIMIT 1");
        $stmt->execute([':track_id' => $track_id]);
        if ($stmt->fetch()) {
            // Track is in a playlist, do not delete
            return false;
        }
        // Safe to delete
        $stmt = $this->connect->prepare("DELETE FROM Track WHERE TrackId = :track_id");
        return $stmt->execute([':track_id' => $track_id]);
    }

}