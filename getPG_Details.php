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
$pg_id = null;
if(isset($headers["Pg_id"])){
$pg_id = $headers["Pg_id"];
$owner_id = null;
}
$response = [];

$pg_query = "SELECT * FROM pg WHERE owner_id = '$owner_id' || pg_id ='$pg_id'";

$result = $conn->query($pg_query);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $address_id = $row["address_id"];
        $address_query = "SELECT * FROM address WHERE address_id = '$address_id'";
        $address_result = $conn->query($address_query);
        $address = "";
        if ($address_result->num_rows > 0) {
            while($address_row = $address_result->fetch_assoc()) {
                $address =$address_row["residence_name"].",".$address_row["street"].",".$address_row["area"].
                ",".$address_row["city"].",".$address_row["state"].", ".$address_row["zip"];
            //   print_r ($address);
                $residence_name = $address_row["residence_name"];
                $street = $address_row["street"];
                $area = $address_row["area"];
                $city = $address_row["city"];
                $state = $address_row["state"];
                $zip = $address_row["zip"];
            }
        }
        $pg_id = $row["pg_id"];
        $image_query = "SELECT image_path FROM images WHERE pg_id = '$pg_id' AND room_id IS NULL;";
        $image_result = $conn->query($image_query);

        $image_path=[];
        if ($image_result->num_rows > 0) {
            while($image_row = $image_result->fetch_assoc()) {
                $image_path[] = $image_row['image_path']; // Store only the image_path value
            }
            $image_path = array_unique($image_path); // Remove duplicates
             // Merge into a string
            // print $image_string;
        }
        $pg_facility= [];
        $pg_amenities = [];
        $pg_facility_query = "SELECT  food, free_wifi, library, parking, lift, daily_cleaning, tv_lounge, 
       laundry, ironing, kitchen, dining_Area, gym, ground, cafeteria, swimming_pool, 
       game_zone, cab_facility, _24_x_7_water,  _24_x_7_electricity, hot_water, ro_purifier, 
       water_cooler, cctv, security_warden, medical_services FROM pg_facilities WHERE pg_id='$pg_id'";
        $pg_facility_result = $conn->query($pg_facility_query);
        if ($pg_facility_result->num_rows > 0) {
            $pg_facility_row = $pg_facility_result->fetch_assoc();
            $pg_amenities = array_keys(array_filter($pg_facility_row, function ($value) {
                return $value == "1"; // Keep only true values
            }));
        
            // Pick only the first 4 amenities
            $selected_amenities = array_slice($pg_amenities, 0, 4);
            
            //     $pg_facility[] = $pg_facility_row;
            // }
            // $pg_facility = array_unique($pg_facility);
            
            // print_r($pg_facility);
        }

        $price_range_query = "SELECT MIN(rent) AS min_price, MAX(rent) AS max_price FROM room WHERE pg_id='$pg_id'";
        $price_range_result = $conn->query($price_range_query);
        $min_price = 0;
        $max_price = 0;
        if ($price_range_result->num_rows > 0) {
        $price_range_row = $price_range_result->fetch_assoc();
        $min_price = $price_range_row["min_price"];
        $max_price = $price_range_row["max_price"];
        $price = '₹'.$min_price .' - '.'₹'.$max_price;
        }

        // print_r($pg_amenities);
        $pg_rules_query = "SELECT * FROM rules WHERE pg_id='$pg_id'";
        $pg_rules_result = $conn->query($pg_rules_query);
        $pg_rules = [];
        if ($pg_rules_result->num_rows > 0) {
            $pg_rules_row = $pg_rules_result->fetch_assoc();
            // print_r($pg_rules_row);
        }

        $pg_charges_query = "SELECT * FROM other_charges WHERE pg_id='$pg_id'";
        $pg_charges_result = $conn->query($pg_charges_query);
        $pg_charges = [];
        if ($pg_charges_result->num_rows > 0) {
            $pg_charges_row = $pg_charges_result->fetch_assoc();
        }
        $avability = 0;
        $room_details_query = "SELECT * FROM room WHERE pg_id='$pg_id'";
        $room_details_result = $conn->query($room_details_query);
        $rooms = [];
        if($room_details_result->num_rows > 0){
            // $room_details_row = $room_details_result->fetch_assoc();
            // print_r($room_details_row);
            while($room_details_row = $room_details_result->fetch_assoc()){

            // $rooms[]=($room_details_row);
            $room_id = $room_details_row["room_id"];
            $avability = $avability + $room_details_row["available_room"];
            // echo $room_id;

            $room_image_query = "SELECT * FROM images WHERE pg_id = '$pg_id' AND room_id = '$room_id'";
            $room_image_result = $conn->query($room_image_query);
            $room_images = [];
            if ($room_image_result->num_rows > 0) {
                while($room_image_row = $room_image_result->fetch_assoc()){
                    $room_images[] = $room_image_row["image_path"];
                    // print_r($room_image_row);
                    // $room_images[] = $room_image_row["image_path"];
                    }
                    
                $room_image_row = $room_image_result->fetch_assoc();
                }
            // print_r($room_images);
            $room_facility_query = "SELECT * FROM room_facilities WHERE room_id= '$room_id'";
            $room_facility_result = $conn->query($room_facility_query);
            $room_facilities = [];
            if ($room_facility_result->num_rows > 0) {
                while($room_facility_row = $room_facility_result->fetch_assoc()){
                    // print_r($room_facility_row);
                    $rooms[] = array_merge($room_details_row, [
                        "room_facilities" => $room_facility_row , "images" => $room_images
                    ]);
                    
                }
 
            // if($room_facility_result->num_rows > 0){

            //     $room_facility_row = $room_facility_result->fetch_assoc();
            //     print_r($room_details_result);  
            }
            }
            // print_r($rooms);
        }

        
        $response[] = array_merge($row,["address"=>$address ,"amenities" => $selected_amenities ,"price" => $price , "availability" => $avability,"images" => $image_path,
        "pg_facilities" => $pg_facility_row , "rules_in_pg" => $pg_rules_row , "other_charges" => $pg_charges_row , "rooms" => $rooms ,
        "residence_name" => $residence_name , "street" => $street , "area" => $area , "city" => $city , "state" => $state , "zip" =>  $zip 
    ]);
       }
        }

echo json_encode(["status" => "success", "owner" => $response], JSON_PRETTY_PRINT);

$conn->close();
?>
