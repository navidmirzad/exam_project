<?php

namespace src\Controllers;
use src\Models\Track;
require_once 'src/Models/Track.php';

class TrackController
{
    private Track $trackModel;

    public function __construct()
    {
        $this->trackModel = new Track();
    }

    // GET /tracks/{search}
    public function search(string $search)
    {
        if (!$search && !(string)$search) {
            $this->sendError('Invalid track search query', 400);
            return;
        }
        try {
            $tracks = $this->trackModel->search($search);
            if (!$tracks) {
                $this->sendError('No tracks found', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($tracks);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getById(int $track_id)
    {
        $id = (int)$track_id;
        if (!$id) {
            $this->sendError('Invalid track ID', 400);
            return;
        }
        try {
            $track = $this->trackModel->getById($id);
            if (!$track) {
                $this->sendError('Track not found', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($track);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getByComposer(string $composer)
    {
        if (!$composer) {
            $this->sendError('Invalid composer name', 400);
            return;
        }
        try {
            $tracks = $this->trackModel->getByComposer($composer);
            if (!$tracks) {
                $this->sendError('No tracks found for this composer', 404);
                return;
            }
            http_response_code(200);
            echo json_encode($tracks);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function create(array $input) 
    {
        $required = ['name', 'album_id', 'media_type_id', 'genre_id', 'composer', 'milliseconds', 'bytes', 'unit_price'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                $this->sendError("Field '$field' is required", 400);
                return;
            }
        }
        try {
            $newId = $this->trackModel->create($input);
            http_response_code(201);
            echo json_encode(['TrackId' => $newId]);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function update(int $track_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !is_array($input)) {
            $this->sendError('Invalid input', 400);
            return;
        }
        try {
            $result = $this->trackModel->update($track_id, $input);
            if ($result) {
                http_response_code(200);
                echo json_encode(['success' => true]);
            } else {
                $this->sendError('No fields to update or update failed', 400);
            }
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function delete(int $track_id)
    {
        try {
            $result = $this->trackModel->delete($track_id);
            if ($result) {
                http_response_code(200);
                echo json_encode(['success' => true]);
            } else {
                $this->sendError('Track is in a playlist or does not exist', 400);
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