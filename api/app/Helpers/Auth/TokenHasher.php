<?php

class TokenHasher {

    public static function hashToken(string $token, string $secret_key) {
        return hash_hmac('sha256', $token, $secret_key);
    }

    public static function encryptToken(string $token, string $secret_key) {
        $cipher = 'aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($token, $cipher, $secret_key, 0, $iv);
        // return $encrypted;
        return base64_encode($iv . "::" . $encrypted);
    }

    public static function decryptToken(string $token, string $secret_key) {
        $cipher = 'aes-256-cbc';
        list($iv, $encrypted) = explode('::', base64_decode($secret_key), 2);
        
        return openssl_decrypt($encrypted, $cipher, $secret_key, 0, $iv);
    }
}