<?php
$servername = "192.168.1.3:3306";
$username = "root";
$password = "root";
$database = "prova";
// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();
?> 