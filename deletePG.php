<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

require 'db.php'; // Database connection file
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        exit(0);
}

$headers = getallheaders();

if (!isset($headers["id"])) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing PG ID"]);
    exit;
}
$ID = $headers["id"];
echo($ID);
$deletePg = " DELETE FROM pg WHERE pg_id = '$ID'";
$conn->query($deletePg);
        $deleteRoom = " DELETE FROM room WHERE room_id = '$ID'";
        $conn->query($deleteRoom);      
        echo json_encode(["status" => "success", "message" => " '$Id' deleted successfully."]);


