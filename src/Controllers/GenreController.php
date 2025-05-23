<?php

namespace src\Controllers;
use src\Models\Genre;
require_once 'src/Models/Genre.php';

class GenreController
{
    private Genre $genreModel;

    public function __construct()
    {
        $this->genreModel = new Genre();
    }

    public function getAll()
    {
        try {
            $genres = $this->genreModel->getAll();
            http_response_code(200);
            echo json_encode($genres);
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
