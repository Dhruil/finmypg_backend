<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

require 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Ensure uploads directory exists
$uploadDir = "uploads/roomImages/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$data = json_decode($_POST["data"], true);
print_r($data);
// Get data from request
$pg_id=$data["pg_id"];
$room_type = $data["room_type"] ?? null;
$available_room = $data["available_room"] ?? null;
$room_size = $data["room_size"] ?? null;
$person_type = $data["person_type"] ?? null;
$gender = $data["gender"] ?? null;
$no_of_rooms = $data["no_of_rooms"] ?? null;
$rent = $data["rent"] ?? null;

if (!$room_type || !$room_size || !$person_type || !$gender || !$no_of_rooms || !$rent) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Insert room details into `rooms` table
$roomQuery = "INSERT INTO room (pg_id, room_type, available_room, room_size, person_type, gender, no_of_rooms, rent) 
              VALUES ('$pg_id', '$room_type', '$available_room', '$room_size', '$person_type', '$gender', '$no_of_rooms', '$rent')";

if (!$conn->query($roomQuery)) {
    echo json_encode(["status" => "error", "message" => "Failed to insert room details"]);
    exit;
}

$room_id = $conn->insert_id; // Get last inserted room ID

// Insert room facilities into `room_facilities` table
$room_facilities = $data["room_facilities"] ?? '{}';
if ($room_facilities) {
    $facilityQuery = "INSERT INTO room_facilities (room_id, ac, tv, wifi, fridge, attached_bathroom, attached_toilets, balcony, wardrobe, safety_locker, study_table, mattress, bed_sheets, pillows) 
                      VALUES ('$room_id', 
                      '".($room_facilities['ac'] ?? 0)."', 
                      '".($room_facilities['tv'] ?? 0)."',
                      '".($room_facilities['wifi'] ?? 0)."',
                      '".($room_facilities['fridge'] ?? 0)."',
                      '".($room_facilities['attached_bathroom'] ?? 0)."',
                      '".($room_facilities['attached_toilets'] ?? 0)."',
                      '".($room_facilities['balcony'] ?? 0)."',
                      '".($room_facilities['wardrobe'] ?? 0)."',
                      '".($room_facilities['safety_locker'] ?? 0)."',
                      '".($room_facilities['study_table'] ?? 0)."',
                      '".($room_facilities['mattress'] ?? 0)."',
                      '".($room_facilities['bed_sheets'] ?? 0)."',
                      '".($room_facilities['pillows'] ?? 0)."')";

    $conn->query($facilityQuery);
}

// Process Multiple Images
$uploadedImages = [];

if (!empty($_FILES["images"]["name"][0])) {
    foreach ($_FILES["images"]["tmp_name"] as $key => $tmpName) {
        if ($_FILES["images"]["error"][$key] == 0) {
            $imageName = time() . "_" . basename($_FILES["images"]["name"][$key]); // Unique file name
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                
                $img = "http://localhost/api/". $targetPath;
                $uploadedImages[] = $img;
                // Insert image path into `room_images` table
                $imageQuery = "INSERT INTO images (pg_id,room_id, image_path) VALUES ('$pg_id', '$room_id', '$img')";
                $conn->query($imageQuery);
            }
        }
    }
}

// Response
echo json_encode([
    "status" => "success",
    "message" => "Room details added successfully",
    "room_id" => $room_id,
    "uploaded_images" => $uploadedImages
]);

$conn->close();
?>
