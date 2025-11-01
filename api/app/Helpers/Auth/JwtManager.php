<?php

namespace App\Helpers\JwtManager;

use MalformedTokenException;
use UnprocessableEntityException;

// Most of the code is from https://medium.com/@selieshjksofficial/creating-and-managing-jwt-tokens-in-php-b6c1fc6c1b46
// ....

class JwtManager {
    private $secret_key;

    public function __construct($secret_key) {
        $this->secret_key = $secret_key;
    }

    public function createToken(array $data): string {
        $base64UrlHeader = $this->base64UrlEncode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($data));
        $base64UrlSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = $this->base64UrlEncode($base64UrlSignature);
        
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    public function validateToken(string $token, bool $validate_exp = true) {
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $this->explodeToken($token);

        $signature = $this->base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret_key, true);
        
        $valid_exp = true;
        if ($validate_exp) {
            $valid_exp = $this->validateTokenExp($base64UrlPayload);
        }

        return hash_equals($signature, $expectedSignature) && $valid_exp;
    }

    private function validateTokenExp(string $base64UrlPayload) {
        $json_data = (array) json_decode($this->base64UrlDecode($base64UrlPayload));
        if (!array_key_exists("exp", $json_data)) {
            return false;
        }

        // True if the expiration time is after current time
        return $json_data["exp"] > time();
    }

    private function explodeToken(string $token): array {
        $exploded_token = explode('.', $token);
        if (sizeof($exploded_token) != 3) {
            // http_response_code(422);
            throw new UnprocessableEntityException(["token" => "InvalidFormat"]);
        }
        return $exploded_token;
    }

    public function decodeToken(string $token): array {
        list(, $base64UrlPayload, ) = explode('.', $token);
        $payload = $this->base64UrlDecode($base64UrlPayload);

        return json_decode($payload, true);
    }

    private function base64UrlEncode($data) {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');

        return rtrim($base64Url, '=');
    }

    private function base64UrlDecode($data) {
        $base64 = strtr($data, '-_', '+/');
        $base64Padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);

        return base64_decode($base64Padded);
    }
}