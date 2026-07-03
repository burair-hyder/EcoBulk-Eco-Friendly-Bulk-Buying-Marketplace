<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "eco_bulk_marketplace";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

session_start();
?>