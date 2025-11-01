<?php

class ErrorHandler {
    public static function handleException(Throwable $exception): void {
        http_response_code(500);
        
        if ($exception instanceof CustomException) {
            $echo_data = [
                "message" => $exception->getMessage(),
                "exception" => get_class($exception)
            ];

            if (!empty($exception->getErrors())) {
                $echo_data["errors"] = $exception->getErrors();
            }
            
            http_response_code($exception->getHttpResponseCode());
            echo json_encode($echo_data);
            return;
        }

        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}