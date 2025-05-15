<?php

namespace src\Controllers;
use src\Models\Artist;
require_once 'src/Models/Artist.php';

class ArtistController
{
    private Artist $artistModel;

    public function __construct()
    {
        $this->artistModel = new Artist();
    }

    // GET /artists 
    public function getAll()
    {
        try {
            $artists = $this->artistModel->getAll();
            http_response_code(200);
            echo json_encode($artists);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    // GET /artists/{artist_id}
    public function getOne(int $artist_id) {
        $id = (int)$artist_id;
        if (!$id) {
            $this->sendError('Invalid artist ID', 400);
            return;
        }
        try {
            $artist = $this->artistModel->getById($id);
            if (!$artist) {
                $this->sendError('Artist not found', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($artist);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    // POST /artists with JSON body { "name": "Artist Name" }
    public function create(string $name)
    {
        if (empty($name)) {
            $this->sendError('Artist name is required', 400);
            return;
        }
        try {
            $newId = $this->artistModel->create($name);
            http_response_code(201);
            echo json_encode(['ArtistId' => $newId, 'Name' => $name]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    // DELETE /artists/{artist_id}
     public function delete(int $artist_id)
    {
        if (!$artist_id) {
            $this->sendError('Invalid artist ID', 400);
            return;
        }

        try {
            if ($this->artistModel->hasAlbums($artist_id)) {
                $this->sendError('Artist has albums and cannot be deleted', 409);
                return;
            }

            if ($this->artistModel->delete($artist_id)) {
                http_response_code(204); // No Content
                echo '';
            } else {
                $this->sendError('Failed to delete artist', 500);
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
