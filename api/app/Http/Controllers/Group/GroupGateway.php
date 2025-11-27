<?php

require_once __DIR__ . "/../Team/BaseTeamGateway.php";

class GroupGateway extends BaseTeamGateway {
    protected const TARGET_TABLE = "Groups";
    protected const TARGET_TABLE_REFERENCE = "id_group";

    public function createTeam(string $state, string $num, string $name) {
        $sql = "INSERT INTO Groups (state, name, num)
                VALUES (:state, :name, :num)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":state", $state, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":num", $num, PDO::PARAM_INT);
        
        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function updateTeam(string $team_id, string $state, string $num, string $name) {
        $sql = "UPDATE Groups
                SET state = :state, num = :num, name = :name
                WHERE id = :team_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":state", $state, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":num", $num, PDO::PARAM_STR);
        $stmt->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        
        $stmt->execute();

        return $this->getTeamFromId($team_id);
    }
}