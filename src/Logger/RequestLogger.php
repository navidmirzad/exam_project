<?php

namespace src\Logger;

class RequestLogger
{
    public static function log()
    {
        // Capture status code after response is sent
        register_shutdown_function(function () {
            $status = http_response_code();
            $logLine = sprintf(
                "[%s] %s %s %s | Status: %s\n",
                date('Y-m-d H:i:s'),
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                file_get_contents('php://input'),
                $status
            );
            file_put_contents(__DIR__ . '/../../request.log', $logLine, FILE_APPEND);
        });
    }
}
