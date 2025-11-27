<?php

use App\Http\Middleware\BaseGateway;

abstract class BaseTeamGateway extends BaseGateway implements TeamGatewayInterface {
    protected const TARGET_TABLE = "Teams";
    protected const TARGET_TABLE_REFERENCE = "id_team";
    protected string $target_table = "";
    protected string $target_table_ref = "";

    public function __construct() {
        $this->target_table = $this::TARGET_TABLE;
        $this->target_table_ref = $this::TARGET_TABLE_REFERENCE;
        return parent::__construct();
    }

    public function deleteTeam(int $team_id) {
        $sql = "DELETE FROM {$this->target_table}
                WHERE id = :team_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":team_id", $team_id, PDO::PARAM_INT);

        $stmt->execute();
    }
    
    public function joinTeam(int $team_id, int $user_id, int $permission_level) {
        $sql = "INSERT INTO {$this->target_table}_Integrantes ({$this->target_table_ref}, id_user, permission_level)
                VALUES (:id_team, :id_user, :permission_level)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_team", $team_id, PDO::PARAM_INT);
        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":permission_level", $permission_level, PDO::PARAM_INT);
        
        $stmt->execute();
    }

    public function quitTeam(int $user_id) {
        $sql = "DELETE FROM {$this->target_table}_Integrantes
                WHERE id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
    }

    public function updateUserPermission(int $team_id, int $user_id, int $permission_level) {
        $sql = "UPDATE {$this->target_table}_Integrantes
                SET permission_level = :permission_level
                WHERE {$this->target_table_ref} = :id_team AND id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_team", $team_id, PDO::PARAM_INT);
        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":permission_level", $permission_level, PDO::PARAM_INT);
        
        $stmt->execute();
    }


    public function getTeamFromId(int $team_id): array {
        $sql = "SELECT 
                    *
                FROM {$this->target_table}
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $team_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    public function getTeamMembers(int $team_id): array {
        $sql = "SELECT 
                    id_user
                FROM {$this->target_table}_Integrantes
                WHERE {$this->target_table_ref} = :id_team";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_team", $team_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (is_bool($result)) {
            return [];
        }

        $members = [];
        foreach (array_values($result) as $data) {
            $members[] = $data["id_user"];
        }
        return $members;
    }

    public function checkTeamExistsId(int $team_id): bool {
        $sql = "SELECT COUNT(id) FROM {$this->target_table}
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $team_id, PDO::PARAM_INT);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }
    
    public function checkTeamExists(string $name): bool {
        $sql = "SELECT COUNT(id) FROM {$this->target_table}
                WHERE name = :name";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }

    public function getUserTeamData(int $user_id): array  {
        $sql = "SELECT {$this->target_table_ref} as id_team, permission_level FROM {$this->target_table}_Integrantes
                WHERE id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }
}