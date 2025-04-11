<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *"); // Set content type to JSON
require 'db.php'; // Include database connection
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

$headers = getallheaders();

if (!isset($headers["User_id"])) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing user_id"]);
    exit;
}
$user_id = $headers["User_id"];
$sql= "SELECT * FROM users WHERE user_id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(["status" => "success", "user" => $user], JSON_PRETTY_PRINT);
}

// if ($result->num_rows > 0) {
//     $row = $result->fetch_assoc();
//         $user =  [
//             "user_id" => $row["user_id"],
//             "name" => $row["owner_name"],
//             "mobile" => $row["mobile"],
//             "email" => $row["email"],
//             "password" => $row["password"],
//             "image" => $row["image"],
//             "no_of_pg_hold" => $row["no_of_pg_hold"],
//             "gender" => $row["gender"],
//             "aadhar_card" => $row["aadhar_card"],
//             "address" => [
//                 "residence_name" => $row["residence_name"],
//                 "street" => $row["street"],
//                 "area" => $row["area"],
//                 "city" => $row["city"],
//                 "state" => $row["state"],
//                 "zip" => $row["zip"]
//             ]
//         ];
    
//     echo json_encode(["status" => "success", "user" => $user], JSON_PRETTY_PRINT);
// } 
    else {
    echo json_encode(["status" => "error", "message" => "No owners found"]);
}

$conn->close();
?>
