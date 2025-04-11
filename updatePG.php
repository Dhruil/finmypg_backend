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
if (!isset($headers["Pg_id"])) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing PG ID"]);
    exit;
}
$pg_id = $headers["Pg_id"];
// print_r($_FILES["image"]);
$uploadDir = "uploads/pgImages/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
// $upload_image;
// if (isset($_FILES["PgImages"])) {
//     $image = $uploadDir .time(). "_" . basename($_FILES["image"]["name"]);
//     // Move the uploaded file
//     if (move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
//         $upload_image = $image;
//       $sql_avatar = "INSERT INTO images (pg_id, image_path) VALUES ('$pg_id', '$image')";
//         $result_avatar = $conn->query($sql_avatar);
//     } else {
//         echo json_encode(["status" => "error", "message" => "Image upload failed"]);
//         exit;
//     }
//     echo json_encode(["status" => "success","uploaded_images" => $upload_image]);
// }

$uploadedImages= [];
if (!empty($_FILES["PgImages"]["name"][0])) {
    foreach ($_FILES["PgImages"]["tmp_name"] as $key => $tmpName) {
        if ($_FILES["PgImages"]["error"][$key] == 0) {
            $imageName = time(). "_" . basename($_FILES["PgImages"]["name"][$key]); // Unique file name
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                // Insert image path into `pg_images` table
                $img = "http://localhost/api/". $targetPath;
                $uploadedImages[] = $img;
                $imageQuery = "INSERT INTO images (pg_id, image_path) VALUES ('$pg_id', '$img')";
                $conn->query($imageQuery);
            }
        }
    }
    echo json_encode(["status" => "success","uploaded_images" => $uploadedImages]);
}

$rmImages = json_decode($_POST["RmImages"], true);
print_r($rmImages);
print_r($_FILES);
if (!empty($rmImages)) {
    $deletedImages = [];

    foreach ($rmImages as $imageUrl) {
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

$data = json_decode($_POST["data"], true);
print_r($data);

if (!empty($data['pg_id'])) {
    $pg_id = $data['pg_id'];
    $address_id = $data['address_id'];
    // Update PG Details

    // Update PG Address
    $query_address = "UPDATE address SET residence_name = '{$data['residence_name']}' ,        street = '{$data['street']}',
        area = '{$data['area']}',
        city = '{$data['city']}',
        state = '{$data['state']}',
        zip = '{$data['zip']}'
        WHERE address_id = '$address_id'";

    $conn->query($query_address);

    $query_pg = "UPDATE pg SET 
        pg_name = '{$data['pg_name']}',
        description = '{$data['description']}',
        operating_since = '{$data['operating_since']}'
        WHERE pg_id = '$pg_id'";
    $conn->query($query_pg);

    // Update PG Facilities
    if (!empty($data['pg_facilities'])) {
        foreach ($data['pg_facilities'] as $facility => $value) {
            $query_facility = "UPDATE pg_facilities SET $facility = '$value' WHERE pg_id = '$pg_id'";
            $conn->query($query_facility);
        }
    }

    // Update PG Rules
    if (!empty($data['rules_in_pg'])) {
        $query_rules = "UPDATE rules SET 
            visitor_allowed = '{$data['rules_in_pg']['visitor_allowed']}',
            non_veg = '{$data['rules_in_pg']['non_veg']}',
            other_gender = '{$data['rules_in_pg']['other_gender']}',
            smoking = '{$data['rules_in_pg']['smoking']}',
            drinking = '{$data['rules_in_pg']['drinking']}',
            party = '{$data['rules_in_pg']['party']}',
            gate_close_time = '{$data['rules_in_pg']['gate_close_time']}'
            WHERE pg_id = '$pg_id'";
        $conn->query($query_rules);
    }

    // Update Other Charges
    if (!empty($data['other_charges'])) {
        $query_charges = "UPDATE other_charges SET 
            electricity = '{$data['other_charges']['electricity']}',
            laundry = '{$data['other_charges']['laundry']}',
            food = '{$data['other_charges']['food']}',
            deposit_amount = '{$data['other_charges']['deposit_amount']}',
            refundable = '{$data['other_charges']['refundable']}',
            notice_period = '{$data['other_charges']['notice_period']}'
            WHERE pg_id = '$pg_id'";
        $conn->query($query_charges);
    }

    echo json_encode(["status" => "success", "message" => "PG details updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "PG ID not provided"]);
}
$conn->close();
?>