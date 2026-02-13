<?php 

require_once __DIR__ . "/BaseTeamGateway.php";
require_once __DIR__ . "/../Auth/AuthGateway.php";

use App\Helpers\JwtManager\JwtManager;
use App\Http\Controllers\Controller;



abstract class BaseTeam extends Controller implements TeamInterface { 
    protected AuthGateway $auth_gateway;
    protected BaseTeamGateway $team_gateway;

    public function __construct() {
        $this->auth_gateway = new AuthGateway();
        // $this->team_gateway = new BaseTeamGateway();
    }

    public function deleteTeam(string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        if (empty($user_team_data)) {
            throw new BadRequestException([], "You must be in a team to delete it");
        }

        $permissions = new TeamPermissions($user_team_data["permission_level"]);
        if (!$permissions->DELETE_PERMISSION) {
            throw new UnauthorizedException([], "You don't have permission to delete this team");
        }

        $this->team_gateway->deleteTeam($user_team_data["id_team"]);

        echo json_encode([
            "message" => "Team deleted successfully"
        ]);
    }

    public function joinTeam(array $route_params, string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        if (!empty($user_team_data)) {
            throw new BadRequestException([], "You are already in a team");
        }

        if (empty($this->team_gateway->getTeamFromId($route_params["id_team"]))) {
            // FIXME: eu não sei se isso aqui funciona na versão do php da Unimar !!
            throw new EntityNotFoundException([], "Couldn't find a team with id of {$route_params['id_team']}");
        }

        $this->team_gateway->joinTeam($route_params["id_team"], $token_data["user_id"], PermissionLevels::USER);
        echo json_encode([
            "message" => "Joined team successfully!"
        ]);
    }
    public function quitTeam(string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        if (empty($user_team_data)) {
            throw new BadRequestException([], "You must be in a team");
        }

        $this->team_gateway->quitTeam($token_data["user_id"]);
        echo json_encode([
            "message" => "Quit team successfully!"
        ]);
    }

    public function transferOwnership(array $body_data, string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);

        if (!$this->auth_gateway->validateUserCredentialsId($token_data["user_id"], $body_data["password"])) {
            throw new UnauthorizedException([], "Wrong password, ownership will not be transfered");
        }
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        $other_user_data = $this->team_gateway->getUserTeamData($body_data["target_user_id"]);
        $this->update_permission_validations($token_data["user_id"], $body_data["target_user_id"], $user_team_data, $other_user_data);

        if ($user_team_data["permission_level"] != PermissionLevels::OWNER) {
            throw new UnauthorizedException([], "You must be the team owner to transfer ownership");
        }

        $team_data = $this->team_gateway->getTeamFromId($user_team_data["id_team"]);
        if ($team_data["name"] != $body_data["team_name"]) {
            throw new UnauthorizedException([], "The team name must be exactly the same as the name of the team");
        }

        $this->team_gateway->updateUserPermission($team_data["id"], $token_data["user_id"], PermissionLevels::MOD);
        $this->team_gateway->updateUserPermission($team_data["id"], $body_data["target_user_id"], PermissionLevels::OWNER);
        echo json_encode([
            "message" => "Transferred ownership to user (id: {$body_data['target_user_id']}) successfully"
        ]);
    }
    public function updateUserpermission(array $body_data, string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_team_data = $this->team_gateway->getUserTeamData($token_data["user_id"]);
        $other_user_data = $this->team_gateway->getUserTeamData($body_data["target_user_id"]);
        $this->update_permission_validations($token_data["user_id"], $body_data["target_user_id"], $user_team_data, $other_user_data);

        if ($other_user_data["permission_level"] >= $user_team_data["permission_level"]) {
            throw new BadRequestException([], "You can't update the permissions of someone with higher or same permissions than you");
        }

        // FIXME: Isso aqui pode ser um problema
        if ($body_data["permission_level"] > $user_team_data["permission_level"]) {
            throw new BadRequestException([], "You can't give more permissions than you have to someone");
        }

        if ($body_data["permission_level"] == PermissionLevels::OWNER && $user_team_data["permission_level"] == PermissionLevels::OWNER) {
            throw new BadRequestException([], "Try using the transfer ownership route");
        }

        $this->team_gateway->updateUserPermission($user_team_data["id_team"], $body_data["target_user_id"], $body_data["permission_level"]);
        echo json_encode([
            "message" => "Updated user permission level to {$body_data['permission_level']} successfully"
        ]);
    }

    public function getTeamInfo(array $route_params) {
        if (!$this->team_gateway->checkTeamExistsId($route_params["id_team"])) {
            throw new EntityNotFoundException([], "This team doesn't exist");
        }
        
        $team_data = $this->team_gateway->getTeamFromId($route_params["id_team"]);

        echo json_encode([
            "message" => "Got team data successfully",
            "data" => $team_data
        ]);
    }
    public function getTeamMembers(array $route_params) {
        if (!$this->team_gateway->checkTeamExistsId($route_params["id_team"])) {
            throw new EntityNotFoundException([], "This team doesn't exist");
        }

        $team_members = $this->team_gateway->getTeamMembers($route_params["id_team"]);
        echo json_encode([
            "message" => "Got team members successfully",
            "members" => $team_members
        ]);
    }
    public function getUserTeamData(array $route_params) {
        $user_team_data = $this->team_gateway->getUserTeamData($route_params["user_id"]);
        if (empty($user_team_data)) {
            throw new EntityNotFoundException([], "The user is not in a team");
        }

        echo json_encode([
            "message" => "Got user team data successfully",
            "data" => $user_team_data
        ]);
    }

    protected function update_permission_validations(int $user_id, int $target_user_id, array $user_team_data, array $other_user_data) {
        if (empty($user_team_data)) {
            throw new BadRequestException([], "You must be in a team to update user permissions");
        }

        if ($target_user_id == $user_id) {
            throw new BadRequestException([], "You can't update your own permissions");
        }

        if (empty($other_user_data)) {
            throw new BadRequestException([], "The target user must be in a team too");
        }

        if ($other_user_data["id_team"] != $user_team_data["id_team"]) {
            throw new BadRequestException([], "The target user must be in the same team as you");
        }
    }

    protected function validate_able_to_create_team(array $body_data, array $token_data) {
        if (!empty($this->team_gateway->getUserTeamData($token_data["user_id"]))) {
            throw new BadRequestException([], "You can't create a troup if you are already in one");
        }

        if ($this->team_gateway->checkTeamExists($body_data["name"])) {
            throw new DuplicateEntityException([], "Another troup already took this name");
        }
    }

    protected function validate_update_team_permissions(array $user_team_data) {
        if (empty($user_team_data)) {
            throw new BadRequestException([], "You must be in a team to update it");
        }

        $permissions = new TeamPermissions($user_team_data["permission_level"]);
        if (!$permissions->UPDATE_PERMISSION) {
            throw new UnauthorizedException([], "You don't have permission to update this team");
        }
    }

    protected function update_team_check_changed(array $body_data, array $team_data) {
        $changed = false;
        foreach (array_keys($team_data) as $key) {
            if (!array_key_exists($key, $body_data)) {
                continue;
            }

            if (!$changed && $team_data[$key] != $body_data[$key]) {
                $changed = true;
            }
            $team_data[$key] = $body_data[$key];
        }
        if (!$changed) {
            // http_response_code(204);
            echo json_encode(["message" => "No data was changed"]);
            return;
        }
    }
}