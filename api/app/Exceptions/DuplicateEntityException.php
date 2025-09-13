<?php

class DuplicateEntityException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "The entity from this request already exists",
        protected int $http_response_code = 409
    ) 
    {
        parent::__construct($errors);
    }
}