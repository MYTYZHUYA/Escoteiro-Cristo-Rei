<?php

namespace App\Http\Requests;
use App\Routing\Endpoint;

class Request {
    protected string $method = "GET";
    protected string $target_endpoint;

    protected string $authorization_type = "";
    protected string $authorization_token = "";

    protected array $query_data = [];
    protected array $body_data = [];
 
    public function __construct() {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->target_endpoint = $this->getTargetUrlFromRequest(getenv("API_PREFIX"));

        $auth_info = $this->getAuthTokenFromRequest();
        if (!empty($auth_info)) {
            $this->authorization_type = $auth_info[0];
            $this->authorization_token = $auth_info[1];
        }

        $this->body_data = $this->getBodyDataFromRequest();
        $this->query_data = $this->queryToAssociativeArray(explode("?", $this->target_endpoint)[1] ?? null);
    }

    private function getAuthTokenFromRequest(): array {
        $headers = getallheaders();
        if (isset($headers["Authorization"])) {
            $auth_info = explode(" ", $headers["Authorization"]);
            if (sizeof($auth_info) < 2) {
                return [];
            }

            return $auth_info;
        }

        return [];
    }

    private function getBodyDataFromRequest(): array {
        $body_data = (array) json_decode(file_get_contents("php://input"), true);
        if (empty($body_data) & !empty($_REQUEST)) {
            $body_data = $_REQUEST;
        }
        
        return $body_data;
    }

    private function getTargetUrlFromRequest(string $prefix = "app"): string {
        $exploded_uri = explode("/", $_SERVER["REQUEST_URI"]);
        $crop_at = 0;
        for ($idx = 0; $idx < sizeof($exploded_uri); $idx++) {
            if ($exploded_uri[$idx] === $prefix) {
                $crop_at = $idx;
                break;
            }
        }
        $target_endpoint = array_slice($exploded_uri, $crop_at);

        return implode("/", $target_endpoint);
    }

    private function queryToAssociativeArray(?string $query): array {
        $parameters = [];
        if ($query == null) {
            return $parameters;
        }

        $exploded_query = explode("&", $query);
        foreach ($exploded_query as $param_str) {
            $param = explode("=", $param_str);

            $key = $param[0];
            $value = $param[1];
            $parameters[$key] = $value;
        }

        return $parameters;
    }

    
    public function getMethod() {
        return $this->method;
    }

    public function getTargetEndpoint() {
        return $this->target_endpoint;
    }
    
    public function getAuthType() {
        return $this->authorization_type;
    }

    public function getAuthToken() {
        return $this->authorization_token;
    }

    public function getUrlParameters(Endpoint $endpoint) {
        $param_names = $endpoint->getParamNames();
        $exploded_uri = explode("/", $this->target_endpoint);
        
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

    public function getBodyData() {
        return $this->body_data;
    }

    public function getQueryData() {
        return $this->query_data;
    }
}