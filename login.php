<?php
// login.php
error_reporting(E_ALL);
ini_set('display_errors',1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
    $jsonInput = file_get_contents("php://input");
    $data = json_decode($jsonInput, true);
    $type = $data["userType"];
    $email = $data["email"];
    $password = $data["password"];

    // Check if email exists
    if($type == "owner"){
    $query = "SELECT * FROM owner WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row["password"] === $password) {

            echo json_encode(array("message" => "Login successful." , "owner_id" => $row["owner_id"]));
        } else {
            // Invalid password
            echo json_encode(array("message" => "Invalid password."));
        }
    } else {
        // Email does not exist
        echo json_encode(array("message" => "Email does not exist."));
    }
    }
    else{
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row["password"] === $password) {
    
                echo json_encode(array("message" => "Login successful." , "user_id" => $row["user_id"]));
            } else {
                // Invalid password
                echo json_encode(array("message" => "Invalid password."));
            }
        } else {
            // Email does not exist
            echo json_encode(array("message" => "Email does not exist."));
        }
    }
}
catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
}
?>