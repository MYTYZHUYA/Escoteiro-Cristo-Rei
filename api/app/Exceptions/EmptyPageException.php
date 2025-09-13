<?php

class EmptyPageException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "This page is currently empty",
        protected int $http_response_code = 204
    ) 
    {
        parent::__construct($errors);
    }
}