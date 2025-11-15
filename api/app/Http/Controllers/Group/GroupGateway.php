<?php

use App\Http\Middleware\BaseGateway;

// TODO: Fazer isso daqui de alguma forma que eu passe a hierarquia do grupo (se é grupo, tropa ou patrulha)
// e adicionar nas tabelas com base nisso, por hora acho que não teria problema de implementar dessa forma,
// já que o comportamento dos três provavelmente é o mesmo só que com um nível hierarquico diferente.

class GroupGateway extends BaseGateway {
    public function createGroup(string $state, string $num, string $name) {
        $sql = "INSERT INTO Groups (state, name, num)
        VALUES (:state, :name, :num)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":state", $state, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":num", $num, PDO::PARAM_STR);
        
        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function updateGroup(string $group_id, string $state, string $num, string $name) {
        $sql = "UPDATE Groups
        SET state = :state, num = :num, name = :name
        WHERE id = :group_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":state", $state, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":num", $num, PDO::PARAM_STR);
        $stmt->bindValue(":group_id", $group_id, PDO::PARAM_STR);
        
        $stmt->execute();

        return $this->getGroupFromId($group_id);
    }

    public function getGroupFromId(string $group_id): array {
        $sql = "SELECT 
                    *
                FROM Groups
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $group_id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    public function checkGroupExists(string $name): bool {
        $sql = "SELECT COUNT(id) FROM Groups
                WHERE name = :name";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }
}