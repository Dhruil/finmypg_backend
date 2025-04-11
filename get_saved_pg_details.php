<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

$user_id = $_GET['user_id'];
$response = [];

$saved_pg_query = "SELECT pg.pg_id, pg.pg_name, pg.address_id, sp.saved_on
FROM saved_pgs sp
JOIN pg ON sp.pg_id = pg.pg_id
WHERE sp.user_id = ?";
$stmt = $conn->prepare($saved_pg_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pg_id = $row["pg_id"];
    $pg_name = $row["pg_name"];
    $saved_on = date("F j, Y", strtotime($row["saved_on"]));

    // 🏠 Address
    $address_query = "SELECT residence_name, street, area, city, state, zip FROM address WHERE address_id = ?";
    $addr_stmt = $conn->prepare($address_query);
    $addr_stmt->bind_param("i", $row["address_id"]);
    $addr_stmt->execute();
    $addr_result = $addr_stmt->get_result();
    $address_str = "";
    if ($addr_result->num_rows > 0) {
        $addr = $addr_result->fetch_assoc();
        $address_str = $addr["residence_name"] . ", " . $addr["street"] . ", " . $addr["area"] . ", " . $addr["city"] . ", " . $addr["state"] . " - " . $addr["zip"];
    }

    // 🖼 Image (only first one)
    $image_query = "SELECT image_path FROM images WHERE pg_id = ? AND room_id IS NULL LIMIT 1";
    $img_stmt = $conn->prepare($image_query);
    $img_stmt->bind_param("i", $pg_id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    $image_path = "";
    if ($img_result->num_rows > 0) {
        $img_row = $img_result->fetch_assoc();
        $image_path = $img_row["image_path"];
    }

    // 💰 Price Range
    $price_query = "SELECT MIN(rent) AS min_price, MAX(rent) AS max_price FROM room WHERE pg_id = ?";
    $price_stmt = $conn->prepare($price_query);
    $price_stmt->bind_param("i", $pg_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();
    $price_str = "";
    if ($price_result->num_rows > 0) {
        $p = $price_result->fetch_assoc();
        $price_str = '₹' . $p['min_price'] . " - ₹" . $p['max_price'];
    }

    // 🧾 Final Response Format
    $response[] = [
        "id" =>$pg_id,
        "name" => $pg_name,
        "address" => $address_str,
        "image" => $image_path,
        "price" => $price_str,
        "savedOn" => $saved_on
    ];
}

echo json_encode(["status" => "success", "savedPGs" => $response], JSON_PRETTY_PRINT);
$conn->close();
?>
