<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");

require 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

// Validate input
if (
    isset($_POST['id']) &&
    isset($_POST['name']) &&
    isset($_POST['phone']) &&
    isset($_POST['email']) &&
    isset($_POST['password']) &&
    isset($_POST['gender'])
) {
    $user_id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    $upload_dir = "uploads/users/";
    $profile_image_url = null;

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Check and handle avatar upload
    if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === UPLOAD_ERR_OK) {
        $avatar_name = basename($_FILES["avatar"]["name"]);
        $avatar_tmp = $_FILES["avatar"]["tmp_name"];
        $avatar_path = $upload_dir . time() . "_" . $avatar_name;
        $image_url = "http://localhost/api/" . $avatar_path;

        if (move_uploaded_file($avatar_tmp, $avatar_path)) {
            // Update with image URL
            $profile_image_url = $image_url;
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to upload avatar"
            ]);
            exit;
        }
    }

    // Build update query
    $update_query = "UPDATE users 
                     SET name = '$name', phone = '$phone', email = '$email', password = '$password', gender = '$gender'";

    if ($profile_image_url) {
        $update_query .= ", profile_image = '$profile_image_url'";
    }

    $update_query .= " WHERE user_id = '$user_id'";

    $result = $conn->query($update_query);

    if ($result) {
        // Fetch updated user
        $user_result = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'");
        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            echo json_encode([
                "status" => "success",
                "message" => "User updated successfully",
                "user" => $user
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "User updated, but fetch failed"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Update failed: " . $conn->error
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
}

$conn->close();
?>
