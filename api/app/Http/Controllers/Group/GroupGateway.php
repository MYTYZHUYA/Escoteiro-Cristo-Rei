<?php

use App\Http\Middleware\BaseGateway;

class UserTeamStatus {
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

class TeamPermissions {
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