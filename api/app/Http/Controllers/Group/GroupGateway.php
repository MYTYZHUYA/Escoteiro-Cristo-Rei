<?php

use App\Http\Middleware\BaseGateway;

class UserGroupStatus {
    public const ACTIVE = "Ativo";
    public const INACTIVE = "Ativo";
}

// TODO: Mudar isso aqui para ser permissões que podem ser atribuídas à usuários ou cargos (tipo o Discord)
class PermissionLevels {
    public const USER = 0;
    public const MOD = 1;
    public const OWNER = 2;

    public const GROUP_UPDATE_THRESHOLD = 2;
    public const GROUP_DELETE_THRESHOLD = 2;
}

class GroupPermissions {
    public bool $UPDATE_PERMISSION = false;
    public bool $DELETE_PERMISSION = false;

    public function __construct(int $permission_level) {
        $this->UPDATE_PERMISSION = $permission_level >= PermissionLevels::GROUP_UPDATE_THRESHOLD;
        $this->DELETE_PERMISSION = $permission_level >= PermissionLevels::GROUP_DELETE_THRESHOLD;
    }
}

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
        $stmt->bindValue(":num", $num, PDO::PARAM_INT);
        
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
        $stmt->bindValue(":group_id", $group_id, PDO::PARAM_INT);
        
        $stmt->execute();

        return $this->getGroupFromId($group_id);
    }

    public function deleteGroup(int $group_id) {
        $sql = "DELETE FROM Groups
                WHERE id = :group_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":group_id", $group_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function joinGroup(int $group_id, int $user_id, int $permission_level) {
        $sql = "INSERT INTO Grupo_Integrantes (id_group, id_user, permission_level)
                VALUES (:id_group, :id_user, :permission_level)";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_group", $group_id, PDO::PARAM_INT);
        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":permission_level", $permission_level, PDO::PARAM_INT);
        
        $stmt->execute();
    }

    public function quitGroup(int $user_id) {
        $sql = "DELETE FROM Grupo_Integrantes
                WHERE id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
    }

    public function updateUserPermission(int $group_id, int $user_id, int $permission_level) {
        $sql = "UPDATE Grupo_Integrantes
                SET permission_level = :permission_level
                WHERE id_group = :id_group AND id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_group", $group_id, PDO::PARAM_INT);
        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":permission_level", $permission_level, PDO::PARAM_INT);
        
        $stmt->execute();
    }


    public function getGroupFromId(int $group_id): array {
        $sql = "SELECT 
                    *
                FROM Groups
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $group_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    public function getGroupMembers(int $group_id): array {
        $sql = "SELECT 
                    id_user
                FROM Grupo_Integrantes
                WHERE id_group = :id_group";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_group", $group_id, PDO::PARAM_INT);
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

    public function checkGroupExistsId(string $group_id): bool {
        $sql = "SELECT COUNT(id) FROM Groups
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $group_id, PDO::PARAM_INT);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }
    
    public function checkGroupExists(string $name): bool {
        $sql = "SELECT COUNT(id) FROM Groups
                WHERE name = :name";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }

    public function getUserGroupData(int $user_id): array  {
        $sql = "SELECT id_group, permission_level FROM Grupo_Integrantes
                WHERE id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_user", $user_id, PDO::PARAM_INT);
        
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }
}