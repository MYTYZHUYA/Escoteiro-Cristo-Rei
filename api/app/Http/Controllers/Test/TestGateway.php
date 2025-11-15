<?php

use App\Http\Middleware\BaseGateway;

class TestGateway extends BaseGateway {
    public function __construct() {
        parent::__construct();
    }
}