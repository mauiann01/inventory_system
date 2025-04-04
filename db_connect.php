<?php 

$conn = new mysqli('localhost','root','','sales_inventory_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper encoding
$conn->set_charset("utf8mb4");
