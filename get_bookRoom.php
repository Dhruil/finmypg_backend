<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
require 'db.php';


// Get headers and check for user-id
$headers = getallheaders();
$user_id = isset($headers['user-id']) ? $headers['user-id'] : null;

// Exit if user_id is not provided
if (!$user_id) {
    echo json_encode([
        "success" => false,
        "message" => "Missing user-id in headers. Access denied."
    ]);
    exit;
}

$get_query = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY booking_date DESC";
$get_result = $conn->query($get_query);

$bookings = [];

if ($get_result->num_rows > 0) {
    while ($row = $get_result->fetch_assoc()) {
        $bookings[] = $row;
    }

    echo json_encode(["success" => true, "data" => $bookings]);
} else {
    echo json_encode(["success" => false, "message" => "No bookings found for this user."]);
}

$conn->close();
?>
