<?php
require_once 'db.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/Key.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, OPTIONS");

$secret_key = "your_secret_key"; // Use the same key as in ownerLogin.php

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

$headers = getallheaders();
if (!isset($headers["Authorization"])) {
    echo json_encode(["status" => "error", "message" => "Authorization token missing"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers["Authorization"]);

try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
    $owner_id = $decoded->owner_id;

    $sql = "SELECT * FROM owner WHERE owner_id = '$owner_id'";
    $result = $conn->query($sql);
    $owners = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $owners[] = [
                "owner_id" => $row["owner_id"],
                "owner_name" => $row["name"],
                "email" => $row["email"]
            ];
        }
        echo json_encode(["status" => "success", "owners" => $owners]);
    } else {
        echo json_encode(["status" => "error", "message" => "No owners found"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Invalid token"]);
}
?>
