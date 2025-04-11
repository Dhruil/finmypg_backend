<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
require 'db.php';

// Get owner_id from header
$headers = getallheaders();
$owner_id = isset($headers['Owner-Id']) ? $headers['Owner-Id'] : null;

if (!$owner_id) {
    echo json_encode(["success" => false, "message" => "Missing owner ID."]);
    exit;
}

// Step 1: Get all pg_ids for this owner
$pg_query = "SELECT pg_id FROM pg WHERE owner_id = '$owner_id'";
$pg_result = $conn->query($pg_query);

$pg_ids = [];

if ($pg_result->num_rows > 0) {
    while ($row = $pg_result->fetch_assoc()) {
        $pg_ids[] = $row['pg_id'];
    }
} else {
    echo json_encode(["success" => true, "bookings" => []]); // No PGs
    exit;
}

// Step 2: Create IN clause from pg_ids
$pg_id_list = "'" . implode("','", $pg_ids) . "'";

// Step 3: Get bookings related to those PGs
$bookings_query = "SELECT * FROM bookings WHERE pg_id IN ($pg_id_list)";
$bookings_result = $conn->query($bookings_query);

$bookings = [];

if ($bookings_result->num_rows > 0) {
    while ($row = $bookings_result->fetch_assoc()) {
        
        $bookings[] = $row;
    }
}
echo json_encode([
    "success" => true,
    "bookings" => $bookings
]);

$conn->close();
?>
