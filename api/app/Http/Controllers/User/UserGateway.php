<?php

use App\Http\Middleware\BaseGateway;

class UserGateway extends BaseGateway {
    public function __construct() {
        parent::__construct();
    }

    public function getAccount(string $reg): array {
        $sql = "SELECT * FROM Users U 
                WHERE reg = :reg";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":reg", $reg, PDO::PARAM_STR);
        
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }
}