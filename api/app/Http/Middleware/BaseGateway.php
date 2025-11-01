<?php

namespace App\Http\Middleware;
use App\Internals\Database;
use PDO;

abstract class BaseGateway {
    protected PDO $conn;

    public function __construct()
    {
       $db = new Database(
            getenv("DATABASE_HOST"),
            getenv("DATABASE_NAME"),
            getenv("DATABASE_USER"),
            getenv("DATABASE_PASSWORD")
       );

       $this->conn = $db->getConnection();
    }
}