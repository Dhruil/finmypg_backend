<?php
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',
    'domain' => '', 
    'secure' => false, // Change to true if using HTTPS
    'httponly' => true,
    'samesite' => 'None'
]);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
