<?php

namespace App\Routing;
require_once __DIR__ . "/Endpoint.php";

use App\Helpers\JwtManager\JwtManager;
use App\Routing\Endpoint;
use App\Http\Requests\Request;
use EntityNotFoundException;
use UnauthorizedException;
use UnprocessableEntityException;
use WrongMethodException;
use AuthGateway;

class Router {
    private array $route_config;
    private array $endpoints;

    public function __construct(String $route_ini_path) 
    {   
        $this->route_config = parse_ini_file($route_ini_path, true);
        $this->createEndpoints();
    }

    public function parseRequest() {
        $request = new Request();

        $selected_endpoint = $this->selectEndpoint($request->getTargetEndpoint());
        if (!$this->handleEndpointValidation($request, $selected_endpoint)) { return; }
        
        if (!$this->handleBodyDataValidation($selected_endpoint, $request)) { return; }
        
        if ($selected_endpoint->needsAuth()) {
            $this->handleAuthTokenValidation($request);
        }
        
        $handler_info = $selected_endpoint->instantiateHandler();
        $instance = $handler_info["handler"];
        $function = $handler_info["target_function"];
        
        $instance->parseRequest($request, $selected_endpoint, $instance, $function);
    }

    protected function handleAuthTokenValidation(Request $request) {
        if ($request->getAuthToken() == "") {
            throw new UnauthorizedException(["token" => "Missing"], "You need an Authorization token to access this route");
        }

        $jwt = new JwtManager(getenv("SECRET_KEY"));
        if (!$jwt->validateToken($request->getAuthToken())) {
            throw new UnauthorizedException(["token" => "Invalid"], "Your Authorization token is not valid | Try refreshing your session");
        }

        $auth_gateway = new AuthGateway();
        if (!$auth_gateway->validateSession($request->getBodyData()["refresh_token"])) { return; }
    } 

    protected function handleBodyDataValidation(Endpoint $selected_endpoint, Request $request): bool {
        $errors = $this->validateBodyData($request->getBodyData(), $selected_endpoint);
        if (!empty($errors)) { 
            http_response_code(422);
            echo json_encode($errors);
            return false; 
        }

        return true;
    }

    protected function handleEndpointValidation(Request $request, ?Endpoint $selected_endpoint): bool {
        $target_endpoint = $request->getTargetEndpoint();
        // echo $target_endpoint;

        if ($selected_endpoint == null) {
            $exception = new EntityNotFoundException([], "No matching endpoint for url '$target_endpoint'");
            $exception->setMiscData(["valid_endpoints" => array_keys($this->endpoints)]);
            // var_dump(array_keys($this->endpoints));

            throw $exception;
        }

        if ($selected_endpoint->getMethod() != $request->getMethod()) {
            $raw_url = $selected_endpoint->getRawUrl();
            throw new WrongMethodException("{$selected_endpoint->getMethod()}", [], "Wrong method for endpoint: '$raw_url'");
        }

        $param_errors = $this->validateUrlParameters($selected_endpoint, $target_endpoint);
        if (!empty($param_errors)) {
            throw new UnprocessableEntityException($param_errors);
        }

        return true;
    }

    protected function createEndpoints() {
        foreach (array_keys($this->route_config) as $section) {
            $endpoint = new Endpoint($section, $this->route_config[$section]);
            $this->endpoints[$endpoint->getRawUrl()] = $endpoint;
        }
    }

    protected function selectEndpoint(String $target_endpoint): ?Endpoint {
        foreach ($this->endpoints as $endpoint) {
            if ($this->endpointMatchesUrl($endpoint, $target_endpoint)) {
                return $endpoint;
            }
        }

        return null;
    }

    protected function endpointMatchesUrl(Endpoint $endpoint, String $uri): bool {
        $url = explode("?", $uri);
        $url = empty($url) ? $uri : $url[0];

        $exploded_endpoint = explode("/", $endpoint->getRawUrl());
        if ($exploded_endpoint[sizeof($exploded_endpoint) - 1] == "") {
            $exploded_endpoint = array_slice($exploded_endpoint, 0, sizeof($exploded_endpoint) - 1);
        }

        $exploded_url = explode("/", $url);
        if ($exploded_url[sizeof($exploded_url) - 1] == "") {
            $exploded_url = array_slice($exploded_url, 0, sizeof($exploded_url) - 1);
        }

        $param_indexes = $endpoint->getParamIndexes();
        if (sizeof($exploded_endpoint) != sizeof($exploded_url)) {
            if (sizeof($exploded_endpoint) - sizeof($exploded_url) < 0) {
                return false;
            }

            for ($idx = 0; $idx < sizeof($exploded_endpoint); $idx++) {
                // Skip url positions that are considered parameters
                if (in_array($idx, $param_indexes)) {
                    continue;
                }

                if ($idx >= sizeof($exploded_url)) {
                    return false;
                }

                if ($exploded_endpoint[$idx] != $exploded_url[$idx]) {
                    return false;
                }
            }
        }

        for ($i = 0; $i < sizeof($exploded_endpoint); $i++) {
            if (in_array($i, $param_indexes)) {
                continue;
            }

            if ($exploded_url[$i] != $exploded_endpoint[$i]) {
                return false;
            }
        }

        return true;
    }

    protected function getUrlParameters(Endpoint $endpoint, String $uri) {
        $param_names = $endpoint->getParamNames();
        $exploded_uri = explode("/", $uri);
        
        $last_idx = sizeof($exploded_uri) - 1;
        if (str_contains($exploded_uri[$last_idx], "?")) {
            $exploded_uri[$last_idx] = substr($exploded_uri[$last_idx], 0, strpos($exploded_uri[$last_idx], "?"));
        }
        
        $parameters = [];
        foreach (array_keys($param_names) as $param_name) {
            $param_value = $exploded_uri[$param_names[$param_name]];
            $parameters[$param_name] = $param_value;
        }

        return $parameters;
    }

    protected function validateUrlParameters(Endpoint $endpoint, String $uri): array {
        $param_names = $endpoint->getParamNames();
        $exploded_uri = explode("/", $uri);
        
        $errors = [];
        foreach (array_keys($param_names) as $param_name) {
            $param_idx = $param_names[$param_name];
            if (sizeof($exploded_uri) <= $param_idx) {
                $errors[$param_name] = "missing";
                continue;    
            }

            if ($exploded_uri[$param_idx] == "") {
                $errors[$param_name] = "missing";
                continue;
            }
        }

        return $errors;
    }

    protected function validateBodyData(array $body_data, Endpoint $endpoint): array {
        $errors = [];
        foreach (array_keys($endpoint->getBodyFields()) as $key) {
            if (str_starts_with($key, "?")) {
                continue;
            }

            if (!array_key_exists($key, $body_data)) {
                $errors[$key] = "Missing";
            }
        }

        return $errors;
    }
}