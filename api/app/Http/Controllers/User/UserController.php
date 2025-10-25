<?php

use App\Http\Controllers\Controller;
class UserController extends Controller {
    private UserGateway $gateway;

    public function __construct() {
        $this->gateway = new UserGateway;
    }
}