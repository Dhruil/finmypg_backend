<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'db.php';
$headers = getallheaders();

if (!isset($headers["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing user_id"]);
    exit;
}
$user_id = $headers["user_id"];
$sql = "SELECT pg_id FROM saved_pgs WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

$savedPgs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $savedPgs[] = $row["pg_id"];
}

echo json_encode(["status" => "success","saved_pgs" => $savedPgs]);
?>