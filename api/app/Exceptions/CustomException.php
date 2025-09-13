<?php

class CustomException extends Exception {
    protected $message;
    protected int $http_response_code = 500;
    
    protected array $errors = [];
    protected array $misc_data = [];
    
    public function __construct(array $errors = []) {
        $this->errors = $errors;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getHttpResponseCode(): int {
        return $this->http_response_code;
    }

    public function setMiscData(array $misc_data): void {
        $this->misc_data = $misc_data;
    }

    public function __toString(): string {
        return $this->message;
    }
}