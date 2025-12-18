<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "school_database"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
echo "Connected successfully to phpmyadmin";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



