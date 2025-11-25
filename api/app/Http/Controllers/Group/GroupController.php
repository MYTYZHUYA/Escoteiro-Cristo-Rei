<?php 

use App\Http\Controllers\Controller;
use App\Helpers\JwtManager\JwtManager;
require_once __DIR__ . "/GroupGateway.php";

class GroupController extends Controller {
    protected GroupGateway $group_gateway;
    public function __construct() {
        $this->group_gateway = new GroupGateway();
    }

    // TODO: Transferências de grupo
    // TODO: Add routes for gateway functions 
    public function createGroup(array $body_data, string $auth_token) {
        $errors = $this->checkFieldLengths(
            ["state" => [2, 2], "name" => [5, 128]], $body_data
        );

        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }
        
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);

        if (!empty($this->group_gateway->getUserGroupData($token_data["user_id"]))) {
            throw new BadRequestException([], "You can't create a group if you are already in one");
        }

        if ($this->group_gateway->checkGroupExists($body_data["name"])) {
            throw new DuplicateEntityException([], "Another group already took this name");
        }

        $id_group = $this->group_gateway->createGroup(
            $body_data["state"],
            $body_data["num"],
            $body_data["name"]
        )["id"];

        $this->group_gateway->joinGroup($id_group, $token_data["user_id"], 
            PermissionLevels::OWNER    
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Group created successfully",
            "id_group" => $id_group
        ]);
    }

    public function updateGroup(array $body_data, string $auth_token) {
        $errors = $this->checkFieldLengths(
            ["state" => [2, 2], "name" => [5, 128]], $body_data
        );

        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }

        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_group_data = $this->group_gateway->getUserGroupData($token_data["user_id"]);
        if (empty($user_group_data)) {
            throw new BadRequestException([], "You must be in a group to update it");
        }

        $permissions = new GroupPermissions($user_group_data["permission_level"]);
        if (!$permissions->UPDATE_PERMISSION) {
            throw new UnauthorizedException([], "You don't have permission to update this group");
        }

        $group_data = $this->group_gateway->getGroupFromId($user_group_data["id_group"]);
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

        $this->group_gateway->updateGroup(
            $user_group_data["id_group"], 
            $group_data["state"], 
            $group_data["num"], 
            $group_data["name"]
        );

        http_response_code(200);
        echo json_encode([
            "message" => "Group updated successfully"
        ]);
    }

    public function deleteGroup(string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_group_data = $this->group_gateway->getUserGroupData($token_data["user_id"]);
        if (empty($user_group_data)) {
            throw new BadRequestException([], "You must be in a group to delete it");
        }

        $permissions = new GroupPermissions($user_group_data["permission_level"]);
        if (!$permissions->DELETE_PERMISSION) {
            throw new UnauthorizedException([], "You don't have permission to delete this group");
        }

        $this->group_gateway->deleteGroup($user_group_data["id_group"]);

        echo json_encode([
            "message" => "Group deleted successfully"
        ]);
    }

    // TODO: Add routes on UserController (or here idk) for joining groups
    // TODO: Provavelmente não é qualquer um que pode sair entrando nos grupos
    // depois vai ter que ter algum tipo de verificação ou autorização da chefia para a entrada no grupo
    public function joinGroup(array $route_params, string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_group_data = $this->group_gateway->getUserGroupData($token_data["user_id"]);
        if (!empty($user_group_data)) {
            throw new BadRequestException([], "You are already in a group");
        }

        if (empty($this->group_gateway->getGroupFromId($route_params["id_group"]))) {
            // FIXME: eu não sei se isso aqui funciona na versão do php da Unimar !!
            throw new EntityNotFoundException([], "Couldn't find a group with id of {$route_params['id_group']}");
        }
        // $this->group_gateway->deleteGroup($user_group_data["id_group"]);

        $this->group_gateway->joinGroup($route_params["id_group"], $token_data["user_id"], PermissionLevels::USER);
        echo json_encode([
            "message" => "Joined group successfully!"
        ]);
    }

    public function quitGroup(string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        
        $user_group_data = $this->group_gateway->getUserGroupData($token_data["user_id"]);
        if (empty($user_group_data)) {
            throw new BadRequestException([], "You must be in a group");
        }

        $this->group_gateway->quitGroup($token_data["user_id"]);
        echo json_encode([
            "message" => "Quit group successfully!"
        ]);
    }

    // TODO:
    public function transferToGroup(array $body_data, string $auth_token) {

    }
}