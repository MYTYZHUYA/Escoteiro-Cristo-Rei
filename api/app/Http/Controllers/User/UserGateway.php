<?php

use App\Http\Middleware\BaseGateway;

class UserGateway extends BaseGateway {
    public function __construct() {
        parent::__construct();
    }

    public function getUser(string $id): array {
        $sql = "SELECT U.username, U.name, G.estado, T.name AS TroupName, P.name AS PatrolName, PI.status AS UserPatrolStatus
                FROM Users U 
                LEFT JOIN Patrol_Integrantes PI ON PI.id_user = U.id
                LEFT JOIN Patrols P ON PI.id_patrol = P.id
                LEFT JOIN Troup_Integrantes TI ON TI.id_user = U.id
                LEFT JOIN Troups T ON TI.id_troup = T.id
                LEFT JOIN Grupo_Integrantes GI ON GI.id_user = U.id
                LEFT JOIN Groups G ON GI.id_group = G.id
                WHERE U.id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
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

    public function createAccount(string $reg, string $password, string $username, string $name): array {
        $sql = "INSERT INTO users (username, password)
                VALUES (:username, :password)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);
        
        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function checkUserExists(string $reg): bool {
        $sql = "SELECT COUNT(id) FROM Users
                WHERE reg = :reg";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":reg", $reg, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }
}