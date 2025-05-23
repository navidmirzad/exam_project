<?php

namespace src\Controllers;
use src\Models\MediaType;
require_once 'src/Models/MediaType.php';

class MediaTypeController
{
    private MediaType $mediaTypeModel;

    public function __construct()
    {
        $this->mediaTypeModel = new MediaType();
    }

    public function getAll()
    {
        try {
            $mediaTypes = $this->mediaTypeModel->getAll();
            http_response_code(200);
            echo json_encode($mediaTypes);
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
