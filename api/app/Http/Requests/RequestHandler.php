<?php

use App\Http\Requests\Request;
use App\Routing\Endpoint;

interface RequestHandler {
    public function parseRequest(Request $request, Endpoint $endpoint, RequestHandler $instance, string $target_function);
}