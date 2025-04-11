<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"] ?? null;
$pg_id = $data["pg_id"] ?? null;

if ($user_id && $pg_id) {
    $sql = "INSERT INTO saved_pgs (user_id, pg_id) VALUES ($user_id, $pg_id)";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save PG"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>