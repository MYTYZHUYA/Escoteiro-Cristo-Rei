<?php

use App\Helpers\JwtManager\JwtManager;
use App\Http\Controllers\Controller;
require_once __DIR__ . "/../Auth/AuthGateway.php";

class UserController extends Controller {
    protected UserGateway $gateway;
    protected AuthGateway $auth_gateway;

    public function __construct() {
        $this->gateway = new UserGateway;
        $this->auth_gateway = new AuthGateway;
    }

    protected function createUserData(array $body_data): array {
        # FIXME: Terminar de fazer isso aqui
        $errors = $this->checkFieldLengths(
            ["reg", "username", "password", "name"], 
            [[8, 8], [4, 32], [6, 127], [0, 255]],
            $body_data
        );
        
        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }

        if ($this->gateway->checkUserExists($body_data["reg"])) {
            throw new DuplicateEntityException([], "User already exists");
        }

        $user_data = $this->gateway->createAccount($body_data["reg"], $body_data["password"], $body_data["username"], $body_data["name"]);
        return $user_data;
    }

    public function createAccount(array $body_data) {
        $user_data = $this->createUserData($body_data);
        http_response_code(200);
        echo json_encode([
            "message" => "User created successfully",
            "user_id" => $user_data["id"]
        ]);
    }

    protected function getUserAccountDataFromSession(string $auth_token): array { 
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);

        $account_data = $this->gateway->getAccountFromId($token_data["user_id"]);
        return $account_data;
    }
    public function getAccountData(string $auth_token) {
        $account_data = $this->getUserAccountDataFromSession($auth_token);
        echo json_encode([
            "account_data" => $account_data
        ]);
    }

    # Retorna sempre um array com duas variáveis, nessa ordem:
    # As novas informações da conta, em formato de array associativo
    # Um valor booleano que demonstra se as sessões existentes foram removidas ou não
    protected function updateUserAccountData(string $auth_token, array $body_data): array {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);

        $old_data = $this->gateway->getAccountFromId($token_data["user_id"]);
        foreach ($old_data as $key => $value) {
            if (!array_key_exists($key, $body_data)) {
                $body_data[$key] = $value;
                continue;
            }
            if ($body_data[$key] == "") {
                $body_data[$key] = $value; 
            }
        }
        $errors = $this->checkFieldLengths(
            ["reg", "username", "password", "name"], 
            [[8, 8], [4, 32], [6, 127], [0, 255]],
            $body_data
        );
        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }

        // TODO: Update para outras coisas relacionadas ao usuário
        $account_data = $this->gateway->updateAccountData(
            $token_data["user_id"],
            $body_data["password"],
            $body_data["username"],
            $body_data["name"],
        );
        
        $result = $this->auth_gateway->clearSessions($token_data["user_id"]);
        return [$account_data, $result];
    }

    public function updateAccountData(string $auth_token, array $body_data) {
        $update_result = $this->updateUserAccountData($auth_token, $body_data);

        echo json_encode([
            "account_data" => $update_result[0],
            "result" => $update_result[1]
        ]);
    }

    public function getUser(array $route_params) {
        # FIXME: PLACEHOLDER
        $user_id = $route_params["user_id"];

        http_response_code(200);
        $user_data = $this->gateway->getUser($user_id);
        echo json_encode([
            "message" => "",
            "user_data" => $user_data
        ]);
    }
}