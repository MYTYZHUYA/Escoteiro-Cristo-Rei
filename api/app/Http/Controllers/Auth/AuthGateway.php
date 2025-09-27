<?php

use App\Http\Middleware\BaseGateway;

class AuthGateway extends BaseGateway {
    public function storeSession(int $user_id, string $token_hash, int $exp_date): void {
        $sql = "INSERT INTO active_sessions (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, :exp_date)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(":exp_date", date('Y-m-d H:i:s', $exp_date), PDO::PARAM_STR);

        $stmt->execute();
    }

    public function getSession(string $token_hash): array {
        $sql = "SELECT * FROM active_sessions
                WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function removeSession(string $refresh_token): void {
        $sql = "DELETE FROM active_sessions
                WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $refresh_token, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function sessionExpired(int $session_id): bool {
        $sql = "SELECT expires_at FROM active_sessions
                WHERE 
                    id = :session_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":session_id", $session_id, PDO::PARAM_INT);

        $stmt->execute();
        $exp_date = $stmt->fetch(PDO::FETCH_ASSOC)["expires_at"];
        $exp_unix = strtotime($exp_date);
        
        return $exp_unix > time();
    }

    public function validateUserCredentials(string $username, string $password): bool {
        $sql = "SELECT COUNT(*) FROM users
                WHERE 
                    username = :username AND
                    password = :password";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch(PDO::FETCH_ASSOC)["COUNT(*)"]);
    }
}