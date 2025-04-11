<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
require 'db.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$booking_id = $data['booking_id'] ?? '';
$status = $data['status'] ?? '';

if (!$booking_id || !$status) {
    echo json_encode(["success" => false, "message" => "Missing booking_id or status."]);
    exit;
}

// Update booking status
$update = "UPDATE bookings SET status = '$status' WHERE booking_id = '$booking_id'";
if ($conn->query($update)) {
    echo json_encode(["success" => true, "message" => "Booking status updated."]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
