<?php

use App\Http\Controllers\Controller;
require_once __DIR__ . "/TestGateway.php";

class TestController extends Controller {
    private TestGateway $gateway;

    public function __construct() {
        $this->gateway = new TestGateway();
    }

    public function testMethod(array $route_params, array $body_data, array $query_params) {
        echo json_encode([
            "RouteParams" => $route_params,
            "BodyParams" => $body_data,
            "QueryParams" => $query_params
        ]);
    }
}