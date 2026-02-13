<?php

require_once __DIR__ . "/ChiefGateway.php";
require_once __DIR__ . "/../User/UserGateway.php";
require_once __DIR__ . "/../User/UserController.php";

class ChiefController extends UserController {
    protected ChiefGateway $chief_gateway;
    public function __construct() {
        parent::__construct();
        $this->chief_gateway = new ChiefGateway;
    }

    public function createAccount(array $body_data) {
        $user_data = $this->createUserData($body_data);
        $chief_data = $this->chief_gateway->createChiefAccount($user_data["id"]);

        http_response_code(200);
        echo json_encode([
            "message" => "Chief created successfully",
            "chief_id" => $chief_data["id"]
        ]);
    }

    public function updateAccountData(string $auth_token, array $body_data) {
        $update_result = $this->updateUserAccountData($auth_token, $body_data);

        echo json_encode([
            "account_data" => $update_result[0],
            "result" => $update_result[1]
        ]);
    }

    public function getChief(array $route_params) {
        # FIXME: PLACEHOLDER
        $user_id = $route_params["user_id"];
        $user_data = $this->chief_gateway->getUser($user_id);
        $chief_data = $this->chief_gateway->getChief($user_id);
        
        $data = array_merge($user_data, $chief_data);

        http_response_code(200);
        echo json_encode([
            "message" => "",
            "chief_data" => $data
        ]);
    }
}