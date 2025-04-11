<?php
require_once 'db.php';
require_once 'jwt/src/JWT.php';
require_once 'jwt/src/Key.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

$secret_key = "your_secret_key"; // Use a strong secret key

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonInput = file_get_contents("php://input");
    $data = json_decode($jsonInput, true);
    $email = $data["email"];
    $password = $data["password"];

    $query = "SELECT * FROM owner WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $payload = [
            "owner_id" => $row["owner_id"],
            "owner_name" => $row["name"],
            "iat" => time(),
            "exp" => time() + 3600 // Token expires in 1 hour
        ];

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        echo json_encode([
            "message" => "Login successful.",
            "token" => $jwt
        ]);
    } else {
        echo json_encode(["message" => "Invalid email or password."]);
    }
}
?>
