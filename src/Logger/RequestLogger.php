<?php

namespace src\Logger;

class RequestLogger
{
    public static function log()
    {
        // Set log file path (outside web root for security)
        $logFile = dirname(__DIR__, 2) . '/logs/request.log';

        // Ensure log directory exists
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0775, true);
        }

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
            $logFile = '/var/www/html/exam_project/request.log'; // Use absolute path
            file_put_contents($logFile, $logLine, FILE_APPEND);
        });
    }
}
