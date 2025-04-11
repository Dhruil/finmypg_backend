<?php
// dashboard.php

require_once 'db.php';

session_start();

if (!isset($_SESSION["owner_id"])) {
    echo "sesson not set";
    exit;
}
echo "sesson set";
$owner_id = $_SESSION["owner_id"];
$owner_name = $_SESSION["owner_name"];

echo $owner_name;
// Add dashboard content here

?>