<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Blank password
$dbname = "cms_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Return the connection object for use in other parts of the code
return $conn;
?>
