<?php 

use App\Helpers\JwtManager\JwtManager;
require_once __DIR__ . "/../Team/BaseTeam.php";

require_once __DIR__ . "/GroupGateway.php";
require_once __DIR__ . "/../Auth/AuthGateway.php";

// FIXME: Sair de um grupo também tem que sair de todos os sub grupos (patrulha e tropa)

class GroupController extends BaseTeam {
    protected GroupGateway $group_gateway;
    protected AuthGateway $auth_gateway;
    public function __construct() {
        $this->team_gateway = new GroupGateway();
        $this->group_gateway = $this->team_gateway;
        $this->auth_gateway = new AuthGateway();
    }

    // TODO: Add routes for gateway functions 
    public function createTeam(array $body_data, string $auth_token) {
        $errors = $this->checkFieldLengths(
            ["state" => [2, 2], "name" => [5, 128]], $body_data
        );

        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }
        
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        $this->validate_able_to_create_team($body_data, $token_data);

        $id_group = $this->group_gateway->createTeam(
            $body_data["state"],
            $body_data["num"],
            $body_data["name"]
        )["id"];

        $this->group_gateway->joinTeam($id_group, $token_data["user_id"], 
            PermissionLevels::OWNER    
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Group created successfully",
            "id_group" => $id_group
        ]);
    }

    public function updateTeam(array $body_data, string $auth_token) {
        $errors = $this->checkFieldLengths(
            ["state" => [2, 2], "name" => [5, 128]], $body_data
        );

        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }

        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        $this->validate_update_team_permissions($user_team_data);

        $team_data = $this->team_gateway->getTeamFromId($user_team_data["id_team"]);
        $this->update_team_check_changed($body_data, $team_data);

        $this->group_gateway->updateTeam(
            $user_team_data["id_team"], 
            $team_data["state"], 
            $team_data["num"], 
            $team_data["name"]
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Group updated successfully"
        ]);
    }

    // TODO:
    public function transferToTeam(array $body_data, string $auth_token) {

    }
}