<?php

class WrongMethodException extends CustomException {
    public function __construct(
        string $correct_method,
        array $errors = [],
        $message = "Wrong method for this route",
        int $http_response_code = 405
    ) 
    {
        parent::__construct($errors, $message, $http_response_code);
        $this->setMiscData(["correct_method" => $correct_method]);
    }
}