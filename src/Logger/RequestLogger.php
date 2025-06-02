<?php

namespace src\Logger;

class RequestLogger
{
    public static function log()
    {
        // Absolute log file path
        $logFile = '/var/www/html/exam_project/request.log';

        // Ensure log directory exists
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        // Capture status code after response is sent
        register_shutdown_function(function () use ($logFile) {
            $status = http_response_code();
            $logLine = sprintf(
                "[%s] %s %s %s | Status: %s\n",
                date('Y-m-d H:i:s'),
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI'],
                file_get_contents('php://input'),
                $status
            );
            // Attempt to write to log file, suppress errors if unwritable
            @file_put_contents($logFile, $logLine, FILE_APPEND);
        });
    }
}
