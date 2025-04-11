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
    $name= $data["name"];
    $email = $data["email"];
    $password = $data["password"];
    $phone = $data["phone"];

    // Check if email exists
    if($type === "owner"){
        $sql = "INSERT INTO owner (name, email, mobile, password)
        VALUES ('$name', '$email', '$phone', '$password')";
        $conn->query($sql);
        echo json_encode(["status" => "success", "message" => "$type Registered"]);
    }
    else{
        $sql = "INSERT INTO users (name, email, phone, password)
        VALUES ('$name', '$email', '$phone', '$password')";
        $conn->query($sql); 
        echo json_encode(["status" => "success", "message" => "$type Registered"]);
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