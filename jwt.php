<?php
function generateJWT($payload, $secret) {
    // Header
    $header = json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
    ]);

    // Encode Header
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

    // Encode Payload
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    // Create Signature
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    // Create JWT
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    return $jwt;
}

function verifyJWT($token, $secret) {
    try {
        // Split the token
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return false;
        }

        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        $signatureProvided = $tokenParts[2];

        // Check the expiration time
        $payloadData = json_decode($payload, true);
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }

        // Verify signature
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return ($base64UrlSignature === $signatureProvided);
    } catch (Exception $e) {
        return false;
    }
}

function getJWTPayload($token) {
    try {
        $tokenParts = explode('.', $token);
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        return json_decode($payload, true);
    } catch (Exception $e) {
        return null;
    }
}