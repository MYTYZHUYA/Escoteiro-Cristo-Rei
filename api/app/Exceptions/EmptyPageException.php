<?php

class EmptyPageException extends CustomException {
    public function __construct(
        array $errors = [],
        $message = "This page is currently empty",
        int $http_response_code = 204
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
    }
}