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

    public function getAccountFromId(int $id): array {
        return $this->getAccountData($id, true);    
    }

    public function getAccount(string $reg): array {
        return $this->getAccountData($reg, false);    
    }

    protected function getAccountData(string $query, bool $is_id) {
        $target_query = $is_id ? "id" : "reg";
        $sql = "SELECT 
                    *
                FROM Users U
                LEFT JOIN Chefia C ON C.id_user = U.id
                WHERE U.$target_query = :query";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":query", $query, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    public function createAccount(string $reg, string $email, string $profile_url, string $password, string $username, string $name): array {
        $sql = "INSERT INTO users (reg, email, name, username, password)
                VALUES (:reg, :email, :name, :username, :password)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":reg", $reg, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":profile_url", $profile_url, PDO::PARAM_STR);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        
        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function updateAccountData(int $user_id, string $email, string $profile_url, string $password, string $username, string $name) : array {
        $sql = "UPDATE Users
                SET name = :name, username = :username, password = :password, profile_url = :profile_url, email = :email
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":profile_url", $profile_url, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        
        $stmt->execute();

        return $this->getAccountFromId($user_id);
    }

    public function deleteAccount(int $user_id) {
        $sql = "DELETE FROM Users
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->checkAccountExists($user_id);
    }

    public function checkAccountExists(int $id): bool {
        $sql = "SELECT COUNT(id) FROM Users
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
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