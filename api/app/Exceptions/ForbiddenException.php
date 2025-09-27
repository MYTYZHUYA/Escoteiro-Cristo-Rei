<?php

class ForbiddenException extends CustomException {
    public function __construct(
        array $errors = [],
        $message = "You have invalid credentials",
        int $http_response_code = 403
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}