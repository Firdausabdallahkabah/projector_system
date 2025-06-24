<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your DB username
define('DB_PASS', '');     // Your DB password
define('DB_NAME', 'projector_db'); // Your DB name

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>