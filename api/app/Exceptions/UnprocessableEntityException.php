<?php

class UnprocessableEntityException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "Request data is invalid",
        protected int $http_response_code = 422
    ) 
    {
        parent::__construct($errors);
    }
}