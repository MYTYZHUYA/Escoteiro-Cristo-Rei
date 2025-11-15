<?php

class DuplicateEntityException extends CustomException {
    public function __construct(
        array $errors = [],
        $message = "The entity from this request already exists",
        int $http_response_code = 409
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}