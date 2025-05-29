<?php

namespace src\Controllers;
use src\Models\Playlist;
require_once 'src/Models/Playlist.php';

class PlaylistController
{
    private Playlist $playlistModel;

    public function __construct()
    {
        $this->playlistModel = new Playlist();
    }

    public function getAll()
    {
        try {
            if (isset($_GET['s']) && strlen(trim($_GET['s'])) > 0) {
                $searchText = trim($_GET['s']);
                $playlists = $this->playlistModel->search($searchText);
                http_response_code(200);
                echo json_encode($playlists);
            } else {
                $playlists = $this->playlistModel->getAll();
                http_response_code(200);
                echo json_encode($playlists);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function search(string $name)
    {
        try {
            $playlists = $this->playlistModel->search($name);
            http_response_code(200);
            echo json_encode($playlists);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getById(int $playlist_id)
    {
        $id = (int)$playlist_id;
        if (!$id) {
            $this->sendError('Invalid playlist ID', 400);
            return;
        }
        try {
            $playlist = $this->playlistModel->getById($id);
            if (!$playlist) {
                $this->sendError('Playlist not found', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($playlist);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function create(array $input)
    {
        if (!isset($input['name']) || $input['name'] === '') {
            $this->sendError('Playlist name is required', 400);
            return;
        }
        try {
            $newId = $this->playlistModel->create($input['name']);
            http_response_code(201);
            echo json_encode(['PlaylistId' => $newId, 'Name' => $input['name']]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function addTrack(int $playlist_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['track_id']) || !$input['track_id']) {
            $this->sendError('track_id is required', 400);
            return;
        }
        try {
            $result = $this->playlistModel->addTrack($playlist_id, $input['track_id']);
            if ($result) {
                http_response_code(201);
                echo json_encode(['success' => true]);
            } else {
                $this->sendError('Failed to add track to playlist (maybe already assigned?)', 400);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function removeTrack(int $playlist_id, int $track_id)
    {
        try {
            $result = $this->playlistModel->removeTrack($playlist_id, $track_id);
            if ($result) {
                http_response_code(200);
                echo json_encode(['success' => true]);
            } else {
                $this->sendError('Track not found in playlist or could not be removed', 400);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function delete(int $playlist_id)
    {
        try {
            $result = $this->playlistModel->delete($playlist_id);
            if ($result) {
                http_response_code(200);
                echo json_encode(['success' => true]);
            } else {
                $this->sendError('Playlist contains tracks or does not exist', 400);
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
