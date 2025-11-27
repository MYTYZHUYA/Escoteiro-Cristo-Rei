<?php 

use App\Helpers\JwtManager\JwtManager;
require_once __DIR__ . "/../Team/BaseTeam.php";
require_once __DIR__ . "/../Team/BaseTeamGateway.php";

require_once __DIR__ . "/GroupGateway.php";
require_once __DIR__ . "/../Auth/AuthGateway.php";

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

        if (!empty($this->group_gateway->getUserTeamData($token_data["user_id"]))) {
            throw new BadRequestException([], "You can't create a group if you are already in one");
        }

        if ($this->group_gateway->checkTeamExists($body_data["name"])) {
            throw new DuplicateEntityException([], "Another group already took this name");
        }

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
            "message" => "Team created successfully",
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
        
        $user_group_data = $this->group_gateway->getUserTeamData($token_data["user_id"]);
        if (empty($user_group_data)) {
            throw new BadRequestException([], "You must be in a group to update it");
        }

        $permissions = new TeamPermissions($user_group_data["permission_level"]);
        if (!$permissions->UPDATE_PERMISSION) {
            throw new UnauthorizedException([], "You don't have permission to update this group");
        }

        $group_data = $this->group_gateway->getTeamFromId($user_group_data["id_group"]);
        $changed = false;
        foreach (array_keys($group_data) as $key) {
            if (!array_key_exists($key, $body_data)) {
                continue;
            }

            if (!$changed && $group_data[$key] != $body_data[$key]) {
                $changed = true;
            }
            $group_data[$key] = $body_data[$key];
        }
        if (!$changed) {
            // http_response_code(204);
            echo json_encode(["message" => "No data was changed"]);
            return;
        }

        $this->group_gateway->updateTeam(
            $user_group_data["id_group"], 
            $group_data["state"], 
            $group_data["num"], 
            $group_data["name"]
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Team updated successfully"
        ]);
    }

    // TODO:
    public function transferToTeam(array $body_data, string $auth_token) {

    }
}