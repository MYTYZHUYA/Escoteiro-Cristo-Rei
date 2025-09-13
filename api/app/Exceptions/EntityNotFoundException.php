<?php

class EntityNotFoundException extends CustomException {
    public function __construct(
        array $errors = [],
        protected $message = "The requested entity couldn't be found",
        protected int $http_response_code = 404
    ) 
    {
        parent::__construct($errors);
    }
}