<?php

require_once __DIR__ . "/../Team/BaseTeamGateway.php";

class TroupGateway extends BaseTeamGateway {
    protected const TARGET_TABLE = "Troups";
    protected const TARGET_TABLE_REFERENCE = "id_troup";

    public function createTeam(int $id_group, string $name) {
        $sql = "INSERT INTO Troups (id_group, name)
                VALUES (:id_group, :name)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_group", $id_group, PDO::PARAM_INT);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        
        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function updateTeam(int $team_id, string $name) {
        $sql = "UPDATE Troups
                SET name = :name
                WHERE id = :team_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        
        $stmt->execute();

        return $this->getTeamFromId($team_id);
    }
}