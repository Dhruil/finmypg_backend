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
$data = json_decode($_POST["data"], true);
print_r($data);
$pg_id = $data["pg_id"];
$owner_id = $data["owner_id"];
$pg_name = $data["pg_name"];
$address_id = $data["address_id"];
$residence_name = $data["residence_name"];
$street = $data["street"];
$area = $data["area"];
$city = $data["city"];
$state = $data["state"];
$zip = $data["zip"];
$map_location = $data["map_location"];
$description = $data["description"];
$operating_since = date("Y-m-d", strtotime($data["operating_since"]));

$addressQuery = "INSERT INTO address (address_id, residence_name, street, area, city, state, zip)
                    VALUES ('$address_id','$residence_name','$street','$area','$city','$state','$zip')";
$conn->query($addressQuery);

$pgQuery = "INSERT INTO pg (pg_id, owner_id, pg_name, address_id, map_location, description, operating_since) 
            VALUES ('$pg_id', '$owner_id', '$pg_name', '$address_id', '$map_location', '$description', '$operating_since')";
$conn->query($pgQuery);


print_r($_FILES["images"]);
$uploadDir = "uploads/pgImages/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadedImages= [];
if (!empty($_FILES["images"]["name"][0])) {
    foreach ($_FILES["images"]["tmp_name"] as $key => $tmpName) {
        if ($_FILES["images"]["error"][$key] == 0) {
            $imageName = time(). "_" . basename($_FILES["images"]["name"][$key]); // Unique file name
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $img = "http://localhost/api/". $targetPath;
                $uploadedImages[] = $img;
                // Insert image path into `pg_images` table
                $imageQuery = "INSERT INTO images (pg_id, image_path) VALUES ('$pg_id', '$img')";
                $conn->query($imageQuery);
            }
        }
    }
}


// Insert PG Facilities
$facilities = $data["pg_facilities"];
$facilityQuery = "INSERT INTO pg_facilities (pg_id, " . implode(", ", array_keys($facilities)) . ") 
                  VALUES ('$pg_id', " . implode(", ", array_map(fn($v) => $v ? "1" : "0", array_values($facilities))) . ")";
$conn->query($facilityQuery);


// Insert PG Rules
$rules = $data["rules_in_pg"];
$ruleQuery = "INSERT INTO rules (pg_id, " . implode(", ", array_keys($rules)) . ") 
              VALUES ('$pg_id', " . implode(", ", array_map(fn($v) => $v === true ? "1" : ($v === false ? "0" : "'$v'"), array_values($rules))) . ")";
$conn->query($ruleQuery);

// Insert Other Charges
$charges = $data["other_charges"];
$chargeQuery = "INSERT INTO other_charges (pg_id, " . implode(", ", array_keys($charges)) . ") 
                VALUES ('$pg_id', '" . implode("', '", array_values($charges)) . "')";
$conn->query($chargeQuery);

// Response
echo json_encode(["status" => "success", "message" => "PG details inserted successfully", "uploaded_images" => $uploadedImages]);

$conn->close();