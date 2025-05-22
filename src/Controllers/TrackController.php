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

    private function sendError(string $message, int $code)
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
    }
}