<?php

namespace App\Routing;

class EndpointFormat {
    public const NEEDS_AUTH = 0;
    public const METHOD = 1;
    public const RAW_URL = 2;
}

class Endpoint {
    private String $method;
    private String $raw_url;
    private array $param_names = [];

    private String $handler_path;
    private array $body_fields = [];

    private bool $needs_auth = false;

    public function __construct(String $section, array $section_content)
    {
        $this->fromSection($section, $section_content);
    }

    private function fromSection(String $section, array $section_content) {
        $splitted_section = explode(" ", $section, 3);
        $offset = -1;
        if (sizeof($splitted_section) == 3) {
            $needs_auth = $splitted_section[EndpointFormat::NEEDS_AUTH];
            if ($needs_auth != "*") {
                http_response_code(500);
                echo json_encode([
                    "message" => "The route at section [$section] should have a '*' instead of an '$needs_auth'"
                ]);
                exit;
            }
            $this->needs_auth = true;
            $offset = 0;
        }

        $method = $splitted_section[EndpointFormat::METHOD + $offset];
        $raw_url = $splitted_section[EndpointFormat::RAW_URL + $offset];
        
        if (array_key_exists("body", $section_content)) {
            $this->body_fields = json_decode($section_content["body"], true);
        }
       
        if ($this->needs_auth) {
            $this->body_fields["refresh_token"] = "string";
        }
        
        $this->handler_path = $section_content["handler"];
        
        $this->method = $method;
        $this->raw_url = $raw_url;
        $this->updateParamNames();
    }

    private function updateParamNames() {
        $exploded_endpoint = explode("/", $this->raw_url);
        for ($idx = 0; $idx < sizeof($exploded_endpoint); $idx++) {
            if (!str_starts_with($exploded_endpoint[$idx], "{")) {
                continue;
            }
            $param_name = "";
            preg_match('/(?<={).*?(?=})/', $exploded_endpoint[$idx], $param_name);

            $this->param_names[$param_name[0]] = $idx;
        }
    }
    

    public function getMethod(): String {
        return $this->method;
    }

    public function needsAuth(): bool {
        return $this->needs_auth;
    }

    public function getRawUrl(): String {
        return getenv("API_PREFIX") . "$this->raw_url";
    }
    
    public function getParamNames(): array {
        return $this->param_names;
    }

    public function getParamIndexes(): array {
        $param_indexes = [];
        foreach (array_keys($this->param_names) as $param_name) {
            $param_indexes[] = $this->param_names[$param_name];
        }

        return $param_indexes;
    }

    public function getHandlerPath(): String {
        return $this->handler_path;
    }
    
    public function getBodyFields(): array {
        return $this->body_fields;
    }

    public function instantiateHandler(): array {
        // Instantiate the handler
        $exploded_path = explode("::", $this->handler_path);
        $instance = new $exploded_path[0]();
        
        return [
            "handler" => $instance,
            "target_function" => $exploded_path[1]
        ];
    }
}