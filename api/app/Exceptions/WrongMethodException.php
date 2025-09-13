<?php

class WrongMethodException extends CustomException {
    public function __construct(
        string $correct_method,
        array $errors = [],
        protected $message = "Wrong method for this route",
        protected int $http_response_code = 405
    ) 
    {
        parent::__construct($errors);
        $this->setMiscData(["correct_method" => $correct_method]);
    }
}