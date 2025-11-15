<?php

namespace App\Internals;
use PDO;

class Database {
    private string $host;
    private string $name; 
    private string $user; 
    private string $password;

    public function __construct(string $host, string $name, string $user, string $password) {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
    }   

    public function getConnection(): PDO {
        return new PDO(
            "mysql:dbname={$this->name};host={$this->host};charset=utf8", 
            $this->user, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}