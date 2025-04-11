<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security keys
define('ENCRYPTION_KEY', 'mySecretKey1234567890123456'); // 32 characters for AES-256
define('JWT_SECRET_KEY', 'your_jwt_secret_key_here_make_it_long_and_random');

// Security configurations
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');