<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
require 'db.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid JSON"]);
        exit;
    }

    // Extract values
    $userId = $data["userId"];
    $pgId = $data["pgId"];
    $pgName = $data["pgName"];
    $address = $data["address"];
    $roomType = $data["roomType"];
    $roomId = $data["roomId"];
    $amount = $data["amount"];
    $checkInDate = date("Y-m-d", strtotime($data["checkInDate"]));
    $checkOutDate = date("Y-m-d", strtotime($data["checkOutDate"]));
    $status = $data["status"];
    $bookingDate = date("Y-m-d", strtotime($data["bookingDate"]));
    $userName = $data["userDetails"]["name"];
    $userEmail = $data["userDetails"]["email"];
    $userPhone = $data["userDetails"]["phone"];
    $specialRequests = $data["specialRequests"];

    // Insert into bookings table
    $insert_query = "INSERT INTO bookings (
        user_id, pg_id, pg_name, address, room_type, room_id, amount, 
        check_in_date, check_out_date, status, booking_date, 
        user_name, user_email, user_phone, special_requests
    ) VALUES (
        '$userId', '$pgId', '$pgName', '$address', '$roomType', '$roomId', '$amount',
        '$checkInDate', '$checkOutDate', '$status', '$bookingDate',
        '$userName', '$userEmail', '$userPhone', '$specialRequests'
    )";

    $insert_result = $conn->query($insert_query);

    if ($insert_result === TRUE) {
        echo json_encode(["success" => true, "message" => "Booking saved successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>

