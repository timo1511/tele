<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the head.php file
include_once 'inc/head.php';

// Function to generate an 8-digit random number
function generateClientCode($conn) {
    $existing_codes = [];
    
    // Query to fetch existing client codes from the database
    $sql = "SELECT client_code FROM client_list";
    $result = $conn->query($sql);
    
    // If the query is successful, fetch existing client codes
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $existing_codes[] = $row['client_code'];
        }
    }
    
    // Generate a random client code and check if it already exists
    do {
        $client_code = mt_rand(10000000, 99999999);
    } while (in_array($client_code, $existing_codes));
    
    // Return the unique client code
    return $client_code;
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables with form data
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'] ?? ''; // Optional field, set to empty string if not provided
    $lastname = $_POST['lastname'];
    $fullname = $lastname . ', ' . $firstname . ' ' . $middlename; // Combine first name, middle name, and last name
    $password = md5($_POST['password']); // Hash the password using md5

    // Generate a client_code
    $client_code = generateClientCode($conn);

    // Prepare SQL statement to insert data into client_list table
    $sql_client_list = "INSERT INTO client_list (client_code, fullname, password, status, date_created, date_updated)
                    VALUES ('$client_code', '$fullname', '$password', 1, NOW(), NOW())";

    // Execute the SQL statement
    if ($conn->query($sql_client_list) === TRUE) {
        // Get the client_id of the newly inserted record
        $client_id = $conn->insert_id;

        // Prepare SQL statements to insert data into client_meta table
        $sql_client_meta = "INSERT INTO client_meta (client_id, meta_field, meta_value) VALUES ";
        $values = [];

        // Iterate through $_POST to extract meta fields and values
        foreach ($_POST as $field => $value) {
            if (!in_array($field, ['lastname', 'firstname', 'middlename', 'password'])) {
                // Escape values to prevent SQL injection
                $field = $conn->real_escape_string($field);
                $value = $conn->real_escape_string($value);

                // Append the values to the $values array
                $values[] = "('$client_id', '$field', '$value')";
            }
        }

        // Concatenate the SQL values if there are values to insert
        if (!empty($values)) {
            $sql_client_meta .= implode(',', $values);

            // Execute the SQL statement for client_meta
            if ($conn->query($sql_client_meta) === TRUE) {
                // Redirect to the login page upon successful registration
                header('Location: onboard.php?client_code=' . urlencode($client_code));
                exit;
            } else {
                // If inserting into client_meta fails, delete the client_list entry
                $conn->query("DELETE FROM client_list WHERE id = '$client_id'");

                // Redirect to an error page
                header('Location: error.php');
                exit;
            }
        } else {
            // If there are no values to insert, handle the error appropriately
            header('Location: error.php');
            exit;
        }
    } else {
        // Redirect to an error page if inserting into client_list fails
        header('Location: error.php');
        exit;
    }
} else {
    // If the form is not submitted, redirect to the registration page
    header('Location: registration.php');
    exit;
}
?>
