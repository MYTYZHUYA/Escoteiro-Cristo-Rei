<?php

class EntityNotFoundException extends CustomException {
    public function __construct(
        array $errors = [],
        $message = "The requested entity couldn't be found",
        int $http_response_code = 404
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}