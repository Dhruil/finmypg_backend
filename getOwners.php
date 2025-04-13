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

if (!isset($headers["Owner_id"])) {
    echo json_encode(["status" => "error", "message" => "Invalid or missing owner_id"]);
    exit;
}
$owner_id = $headers["Owner_id"];
$sql = "
    SELECT 
        o.owner_id, o.name AS owner_name, o.mobile, o.email, o.password, o.image,o.no_of_pg_hold, o.gender, o.aadhar_card,
        a.residence_name, a.street, a.area, a.city, a.state, a.zip
    FROM Owner o
    INNER JOIN address a ON o.address_id = a.address_id
    WHERE o.owner_id = '$owner_id'
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
        $owner =  [
            "id" => $row["owner_id"],
            "name" => $row["owner_name"],
            "mobile" => $row["mobile"],
            "email" => $row["email"],
            "password" => $row["password"],
            "image" => $row["image"],
            "no_of_pg_hold" => $row["no_of_pg_hold"],
            "gender" => $row["gender"],
            "aadhar_card" => $row["aadhar_card"],
            "address" => [
                "residence_name" => $row["residence_name"],
                "street" => $row["street"],
                "area" => $row["area"],
                "city" => $row["city"],
                "state" => $row["state"],
                "zip" => $row["zip"]
            ]
        ];
    
    echo json_encode(["status" => "success", "owner" => $owner], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "error", "message" => "No owners found"]);
}

$conn->close();
?>
