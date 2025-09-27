<?php

class UnprocessableEntityException extends CustomException {
    public function __construct(
        array $errors = [],
        $message = "Request data is invalid",
        int $http_response_code = 422
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}