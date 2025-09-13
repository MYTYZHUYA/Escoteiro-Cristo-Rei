<?php

class ForbiddenException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "You have invalid credentials",
        protected int $http_response_code = 403
    ) 
    {
        parent::__construct($errors);
    }
}