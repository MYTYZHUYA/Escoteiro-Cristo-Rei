<?php

class BadRequestException extends CustomException {
    public function __construct(
        array $errors = [],
        $message = "Bad request, try again",
        int $http_response_code = 400
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}