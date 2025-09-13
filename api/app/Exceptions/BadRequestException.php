<?php

class BadRequestException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "Bad request, try again",
        protected int $http_response_code = 400
    ) 
    {
        parent::__construct($errors);
    }
}