<?php
// db.php

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "findmypg";
// // $servername = "sql210.infinityfree.com";
// // $username = "if0_38719426";
// // $password = "l7SnMuSNkAl";
// // $dbname = "if0_38719426_findmypg";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


$host = 'mysql-1ac2b921-dpldce2021-fd60.k.aivencloud.com';
$port = 26374; // Replace with your Aiven port
$user = 'avnadmin';
$password = 'AVNS_cBTE6rGsinyDLW-vkkN';
$dbname = 'findmypg';

// Path to downloaded CA certificate (on your server)
$ssl_ca = 'ca.pem';

// Create connection
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, $ssl_ca, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}
?>