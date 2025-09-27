<?php

class UnauthorizedException extends CustomException {
    public function __construct(
        array $errors = [],
        string $message = "You can't access this endpoint",
        int $http_response_code = 401
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}