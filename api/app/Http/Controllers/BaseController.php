<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Routing\Endpoint;
use ReflectionMethod;
use RequestHandler;

abstract class BaseController implements RequestHandler {
    public function parseRequest(Request $request, Endpoint $endpoint, RequestHandler $instance, string $target_function) {
        $class = get_class($instance);
        $reflection = new ReflectionMethod("$class::$target_function");
        $function_parameters = $reflection->getParameters();
        $parameter_values = [
            "route_params" => $request->getUrlParameters($endpoint),
            "body_data" => $request->getBodyData(),
            "query_params" => $request->getQueryData(),
            "auth_token" => $request->getAuthToken()
        ];
        $args = [];
        foreach ($function_parameters as $param) {
            $param_name = $param->getName();
            if (array_key_exists($param_name, $parameter_values)) {
                $args[$param_name] = $parameter_values[$param_name];
            }
        }

        $instance->$target_function(...$args);
    }
}