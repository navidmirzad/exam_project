<?php

namespace src\Controllers;

use src\Models\Album;

require_once 'src/Models/Album.php';

class AlbumController
{
    private Album $albumModel;

    public function __construct()
    {
        $this->albumModel = new Album();
    }

    public function getAll()
    {
        try {
            if (isset($_GET['s']) && strlen(trim($_GET['s'])) > 0) {
                $searchText = trim($_GET['s']);
                $albums = $this->albumModel->getByTitle($searchText);
            } else {
                $albums = $this->albumModel->getAll();
                http_response_code(200);
                echo json_encode($albums);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function search(string $title)
    {
        try {
            $albums = $this->albumModel->getByTitle($title);
            http_response_code(200);
            echo json_encode($albums);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getById(int $albumId)
    {
        try {
            $album = $this->albumModel->getById($albumId);
            if (!$album) {
                $this->sendError('Album not found', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($album);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getAlbumAndTracks(int $albumId)
    {
        try {
            $album = $this->albumModel->getAlbumAndTracks($albumId);
            if (!$album) {
                $this->sendError('Album not found', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($album);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['title']) || !isset($input['artist_id'])) {
            $this->sendError('Missing title or artist_id', 400);
            return;
        }

        $title = $input['title'];
        $artistId = (int)$input['artist_id'];

        try {
            $newId = $this->albumModel->create($title, $artistId);
            $createdAlbum = $this->albumModel->getById($newId);
            http_response_code(201);
            echo json_encode($createdAlbum);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function update(int $albumId, array $input)
    {
        if (!$albumId) {
            $this->sendError('Missing album ID', 400);
            return;
        }

        // Fetch current album data
        $current = $this->albumModel->getById($albumId);
        if (!$current) {
            $this->sendError('Album not found', 404);
            return;
        }

        // Use provided values or fallback to current values
        $title = isset($input['title']) ? $input['title'] : $current['Title'];
        $artistId = isset($input['artist_id']) ? (int)$input['artist_id'] : $current['ArtistId'];

        if (empty($title) || !$artistId) {
            $this->sendError('Missing required fields', 400);
            return;
        }

        try {
            $success = $this->albumModel->update($albumId, $title, $artistId);
            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'Album updated successfully']);
            } else {
                $this->sendError('Failed to update album', 500);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function delete(int $albumId)
    {
        if (!$albumId) {
            $this->sendError('Invalid album ID', 400);
            return;
        }

        try {
            if ($this->albumModel->hasTracks($albumId)) {
                $this->sendError('Album has tracks and cannot be deleted', 409);
                return;
            }

            if ($this->albumModel->delete($albumId)) {
                http_response_code(200);
                echo json_encode(['message' => "Album with ID: $albumId - deleted successfully"]);
            } else {
                $this->sendError('Failed to delete album', 500);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function sendError(string $message, int $code)
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
    }
}
