<?php
// TODO: Fazer uma pasta utils global ou carregar o .env automaticamente sla
namespace App\Utils;

class DotEnv {
    public static function parseDotEnv(String $file_path) {
        if (!file_exists($file_path)) {
            http_response_code(500);
            echo json_encode([
                "message" => "Failed to load .env file | Invalid file path"
            ]);
            exit;
        }

        $cfg = parse_ini_file($file_path);
        foreach (array_keys($cfg) as $key) {
            $value = $cfg[$key];
            putenv("$key=$value");
        }
    }
}