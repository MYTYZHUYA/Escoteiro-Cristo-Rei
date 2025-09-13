<?php

class UnauthorizedException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "You can't access this endpoint",
        protected int $http_response_code = 401
    ) 
    {
        parent::__construct($errors);
    }
}