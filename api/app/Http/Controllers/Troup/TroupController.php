<?php 

use App\Helpers\JwtManager\JwtManager;
require_once __DIR__ . "/../Team/BaseTeam.php";

require_once __DIR__ . "/TroupGateway.php";
require_once __DIR__ . "/../Group/GroupGateway.php";
require_once __DIR__ . "/../Auth/AuthGateway.php";

class TroupController extends BaseTeam {
    protected TroupGateway $troup_gateway;
    protected GroupGateway $group_gateway;
    protected AuthGateway $auth_gateway;
    public function __construct() {
        $this->team_gateway = new TroupGateway();
        $this->troup_gateway = $this->team_gateway;
        $this->group_gateway = new GroupGateway();
        $this->auth_gateway = new AuthGateway();
    }

    // TODO: Add routes for gateway functions 
    public function createTeam(array $body_data, string $auth_token) {
        $errors = $this->checkFieldLengths(
            ["name" => [5, 128]], $body_data
        );

        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }
        
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);

        $user_group_data = $this->group_gateway->getUserTeamData($token_data["user_id"]);
        if (empty($user_group_data)) {
            throw new BadRequestException([], "You must be in a group to create a troup");
        }

        $this->validate_able_to_create_team($body_data, $token_data);

        $id_troup = $this->troup_gateway->createTeam(
            $user_group_data["id_team"],
            $body_data["name"]
        )["id"];

        $this->troup_gateway->joinTeam($id_troup, $token_data["user_id"], 
            PermissionLevels::OWNER    
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Troup created successfully",
            "id_troup" => $id_troup
        ]);
    }

    public function updateTeam(array $body_data, string $auth_token) {
        $errors = $this->checkFieldLengths(
            ["name" => [5, 128]], $body_data
        );

        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }

        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_team_data = $this->troup_gateway->getUserTeamData($token_data["user_id"]);
        $this->validate_update_team_permissions($user_team_data);

        $team_data = $this->troup_gateway->getTeamFromId($user_team_data["id_team"]);
        $this->update_team_check_changed($body_data, $team_data);

        $this->troup_gateway->updateTeam(
            $user_team_data["id_team"], 
            $team_data["name"]
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Troup updated successfully"
        ]);
    }

    public function joinTeam(array $route_params, string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_group_data = $this->group_gateway->getUserTeamData($token_data["user_id"]);
        if (empty($user_group_data)) {
            throw new BadRequestException([], "You must be in a group to join a troup");
        }
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        if (!empty($user_team_data)) {
            throw new BadRequestException([], "You are already in a troup");
        }

        if (empty($this->team_gateway->getTeamFromId($route_params["id_team"]))) {
            // FIXME: eu não sei se isso aqui funciona na versão do php da Unimar !!
            throw new EntityNotFoundException([], "Couldn't find a troup with id of {$route_params['id_team']}");
        }

        $troup_data = $this->troup_gateway->getTeamFromId($route_params["id_team"]);
        if ($troup_data["id_group"] != $user_group_data["id_team"]) {
            throw new BadRequestException([], "You can't join a troup from another group");
        }

        $this->team_gateway->joinTeam($route_params["id_team"], $token_data["user_id"], PermissionLevels::USER);
        echo json_encode([
            "message" => "Joined troup successfully!"
        ]);
    }

    // TODO:
    public function transferToTeam(array $body_data, string $auth_token) {

    }
}