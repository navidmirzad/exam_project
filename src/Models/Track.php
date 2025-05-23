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

    public function getById(int $id)
    {
        $stmt = $this->connect->prepare("
            SELECT 
                track.TrackId, 
                track.Name, 
                track.AlbumId, 
                track.MediaTypeId, 
                mediatype.Name AS MediaTypeName, 
                track.GenreId, 
                genre.Name AS GenreName, 
                track.Composer, 
                track.Milliseconds, 
                track.Bytes, 
                track.UnitPrice
            FROM track
            JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
            JOIN genre ON track.GenreId = genre.GenreId
            WHERE track.TrackId = :id
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
            INSERT INTO track 
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
        $sql = "UPDATE track SET " . implode(', ', $fields) . " WHERE TrackId = :track_id";
        $stmt = $this->connect->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $track_id)
    {
        // Check if the track exists in any playlist
        $stmt = $this->connect->prepare("SELECT 1 FROM playlisttrack WHERE TrackId = :track_id LIMIT 1");
        $stmt->execute([':track_id' => $track_id]);
        if ($stmt->fetch()) {
            // Track is in a playlist, do not delete
            return false;
        }
        // Safe to delete
        $stmt = $this->connect->prepare("DELETE FROM track WHERE TrackId = :track_id");
        return $stmt->execute([':track_id' => $track_id]);
    }

}