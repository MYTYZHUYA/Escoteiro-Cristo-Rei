<?php

use App\Http\Controllers\Controller;
class UserController extends Controller {
    private UserGateway $gateway;

    public function __construct() {
        $this->gateway = new UserGateway;
    }

    public function createAccount(array $body_data) {
        # FIXME: Terminar de fazer isso aqui
        $errors = $this->checkFieldLengths(["reg", "username", "password", "name"], [[8, 8], [4, 32], [6, 127], [0, 255]],$body_data);
        if (!empty($errors)) {
            throw new UnprocessableEntityException($errors);
        }
    }

    public function getUser(array $route_params) {
        # FIXME: PLACEHOLDER
        $user_id = $route_params["user_id"];

        echo json_encode($this->gateway->getUser($user_id));
    }
}