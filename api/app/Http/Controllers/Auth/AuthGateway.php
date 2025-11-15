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

    private function getSession(string $token_hash): array {
        $sql = "SELECT * FROM active_sessions
                WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
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

    public function validateUserCredentials(string $reg, string $password): bool {
        $sql = "SELECT password FROM Users
                WHERE 
                    reg = :reg";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":reg", $reg, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result == false) {
            return false;
        }
        
        return password_verify($password, $result["password"]);
    }

    public function clearSessions(string $user_id) {
        $sql = "DELETE FROM active_sessions
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch();
        return is_bool($result) ? [] : $result;
    }

    public function getSessionData(string $refresh_token) : array {
        $token_hash = TokenHasher::hashToken($refresh_token, getenv("SECRET_KEY"));
        $session_data = $this->getSession($token_hash);

        if ($this->checkSessionData($session_data)) {
            return $session_data;
        };

        return [];
    }

    public function validateSession(string $refresh_token) : bool {
        $token_hash = TokenHasher::hashToken($refresh_token, getenv("SECRET_KEY"));
        $session_data = $this->getSession($token_hash);

        return $this->checkSessionData($session_data);
    }

    private function checkSessionData(array $session_data): bool {
        if ($session_data == []) {
            throw new UnauthorizedException(["session" => "non-existent"]);
        }

        if ($this->sessionExpired((int) $session_data["id"])) {
            throw new ForbiddenException([], "Your session expired, please log in again to create a new session");
        }
        return true;
    }
}