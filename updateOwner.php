<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

require 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

// Read JSON input
// $data = json_decode(file_get_contents("php://input"), true);

// Validate input
// if (!isset($data)) {
//     echo json_encode(["status" => "error", "message" => "Missing required fields"]);
//     exit;
// }
// print_r($data);
$owner_id = $_POST['id'];
$name = $_POST['name'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$password = $_POST['password'];// Keeping plain text (as per your style)
$no_of_pg_hold = $_POST['no_of_pg_hold'];
$gender = $_POST['gender'];
$aadhar_card = $_POST['aadhar_card'];

// Address Fields
$residence_name = $_POST['residence_name'];
$street = $_POST['street'];
$area = $_POST['area'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$upload_dir = "uploads/owners/";
$result_avatar = null ;
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
// Start transaction
mysqli_query($conn, "START TRANSACTION");
if (isset($_FILES["avatar"])) {
    $avatar = $upload_dir . basename($_FILES["avatar"]["name"]);
    $image = 'http://localhost/api/'.$avatar;
    // Move the uploaded file
    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar)) {
        // Update avatar path in database
      $sql_avatar = "UPDATE Owner 
        SET  image = '$image'  WHERE owner_id = '$owner_id'";
        $result_avatar = $conn->query($sql_avatar);
    } else {
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        exit;
    }
}



// Update Owner Table
$sql_owner = "UPDATE Owner 
              SET name = '$name', mobile = '$mobile', email = '$email', password = '$password',
                  no_of_pg_hold = '$no_of_pg_hold', gender = '$gender', aadhar_card  = '$aadhar_card' 
              WHERE owner_id = '$owner_id'";
$result_owner =  $conn->query($sql_owner);

// Update Address Table
$sql_address = "UPDATE address 
                SET residence_name = '$residence_name', street = '$street', area = '$area', 
                    city = '$city', state = '$state', zip = '$zip'  
                WHERE address_id = (SELECT address_id FROM Owner WHERE owner_id = '$owner_id')";
$result_address = $conn->query($sql_address);

$sql = "
    SELECT 
        o.owner_id, o.name AS owner_name, o.mobile, o.email, o.password, o.image,o.no_of_pg_hold, o.gender, o.aadhar_card,
        a.residence_name, a.street, a.area, a.city, a.state, a.zip
    FROM Owner o
    INNER JOIN address a ON o.address_id = a.address_id
    WHERE o.owner_id = '$owner_id'
";
if ($result_owner && $result_address) {
    mysqli_query($conn, "COMMIT");
    $result = $conn->query($sql);
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
    echo json_encode(["status" => "success", "message" => "Owner details updated successfully","owner" => $owner]);
} else {
    mysqli_query($conn, "ROLLBACK");
    echo json_encode(["status" => "error", "message" => "Update failed"]);
}

// Close connection
mysqli_close($conn);
?>
