<?php
// initialize.php

require_once 'db/config.php'; // Adjust the path as necessary

// Function to establish a database connection
function dbConnect() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// You can then use dbConnect() wherever you need a database connection
// $conn = dbConnect();
