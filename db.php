<?php
// db.php

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "findmypg";
$servername = "sql101.free2host.eu.org";
$username = "usesr_38719965";
$password = "00389195fd3402a";
$dbname = "usesr_38719965_findmypg";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>