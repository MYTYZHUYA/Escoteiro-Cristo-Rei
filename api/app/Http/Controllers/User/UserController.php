<?php

use App\Helpers\JwtManager\JwtManager;
use App\Http\Controllers\Controller;
require_once __DIR__ . "/../Auth/AuthGateway.php";

class UserController extends Controller {
    private UserGateway $gateway;
    private AuthGateway $auth_gateway;

    public function __construct() {
        $this->gateway = new UserGateway;
        $this->auth_gateway = new AuthGateway;
    }

    public function createAccount(array $body_data) {
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
        http_response_code(200);
        echo json_encode([
            "message" => "User created successfully",
            "user_data" => $user_data
        ]);
    }

    public function getAccountData(string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);

        $account_data = $this->gateway->getAccountFromId($token_data["user_id"]);
        echo json_encode([
            "account_data" => $account_data
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