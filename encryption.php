<?php
function decryptData($input, $key) {
    try {
        $iv = base64_decode($input['iv']);
        $encryptedData = base64_decode($input['data']);
        
        if (!$iv || !$encryptedData) {
            return false;
        }

        $decrypted = openssl_decrypt(
            $encryptedData,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted ?: false;
    } catch (Exception $e) {
        error_log("Decryption error: " . $e->getMessage());
        return false;
    }
}

function encryptData($data, $key) {
    try {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return [
            'iv' => base64_encode($iv),
            'data' => base64_encode($encrypted)
        ];
    } catch (Exception $e) {
        error_log("Encryption error: " . $e->getMessage());
        return false;
    }
}