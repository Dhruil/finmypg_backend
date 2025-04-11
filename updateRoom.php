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
// $headers = getallheaders();
// if (!isset($headers["Pg_id"])) {
//     echo json_encode(["status" => "error", "message" => "Invalid or missing PG ID"]);
//     exit;
// }
// $pg_id = $headers["Pg_id"];
// // print_r($_FILES["image"]);
$uploadDir = "uploads/roomImages/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
$data = json_decode($_POST["data"], true);

$RmRoomImages = json_decode($_POST["RmRoomImages"], true);

if (!empty($_FILES["RoomImages"]["name"][0])) {
    print_r($_FILES);
}

if (!empty($data['pg_id'])) {
    $pg_id = $data['pg_id'];
    $room_id = $data['room_id'];
    echo $pg_id;
    echo $room_id;

    $roomQuery = "UPDATE room SET room_type = '{$data['room_type']}', 
    available_room = '{$data['available_room']}',
    room_size = '{$data['room_size']}', person_type = '{$data['person_type']}', gender = '{$data['gender']}', 
    no_of_rooms = '{$data['no_of_rooms']}', rent = '{$data['rent']}' WHERE room_id = '$room_id'" ;

    $conn->query($roomQuery);
    
    if (!empty($data['room_facilities'])) {
        foreach ($data['room_facilities'] as $facility => $value) {
            $query_facility = "UPDATE room_facilities SET $facility = '$value' WHERE room_id = '$room_id'";
            $conn->query($query_facility);
        }
    }

    $uploadedImages = [];

if (!empty($_FILES["RoomImages"]["name"][0])) {
    foreach ($_FILES["RoomImages"]["tmp_name"] as $key => $tmpName) {
        if ($_FILES["RoomImages"]["error"][$key] == 0) {
            $imageName = time() . "_" . basename($_FILES["RoomImages"]["name"][$key]); // Unique file name
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

if (!empty($RmRoomImages)) {
    $deletedImages = [];

    foreach ($RmRoomImages as $imageUrl) {
        // Extract filename from URL
        $filePath = str_replace("http://localhost/api/", "", $imageUrl);

        // Delete from database
        $query = "DELETE FROM images WHERE image_path = '$imageUrl'";
        $conn->query($query); // Execute query

        // Delete from system folder
        if (file_exists($filePath)) {
            unlink($filePath);
            $deletedImages[] = $filePath; // Store deleted files for confirmation
        }
    }

    echo json_encode(["status" => "success", "deleted" => $deletedImages]);
} else {
    echo json_encode(["status" => "error", "message" => "No images provided"]);
}
    echo json_encode(["status" => "success", "message" => "Room details updated successfully"]);
}else {
    echo json_encode(["status" => "error", "message" => "PG ID not provided"]);}
$conn->close();
?>