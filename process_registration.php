<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection or other necessary files
include_once 'inc/head.php';

// Function to generate a unique client code
function generateClientCode($conn) {
    $existing_codes = [];
    $sql = "SELECT client_code FROM client_list";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $existing_codes[] = $row['client_code'];
        }
    }
    
    do {
        $client_code = mt_rand(10000000, 99999999);
    } while (in_array($client_code, $existing_codes));
    
    return $client_code;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and process input data
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $middlename = isset($_POST['middlename']) ? $conn->real_escape_string($_POST['middlename']) : '';
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $fullname = $lastname . ', ' . $firstname . ' ' . $middlename;
    $password = md5($_POST['password']); // Securely hash the password

    $client_code = generateClientCode($conn); // Generate a unique client code

    // Insert basic client information into client_list
    $sql_client_list = "INSERT INTO client_list (client_code, fullname, password, status, date_created, date_updated)
                        VALUES ('$client_code', '$fullname', '$password', 1, NOW(), NOW())";

    if ($conn->query($sql_client_list)) {
        $client_id = $conn->insert_id; // Retrieve the newly created client_id

        // Prepare SQL for client_meta insertion including firstname, middlename, and lastname
        $meta_fields = ['firstname' => $firstname, 'middlename' => $middlename, 'lastname' => $lastname];
        foreach ($_POST as $field => $value) {
            if (!in_array($field, ['password'])) { // Exclude password from client_meta
                $meta_fields[$field] = $conn->real_escape_string($value);
            }
        }

        // Construct SQL parts for client_meta
        $sql_client_meta_parts = [];
        foreach ($meta_fields as $field => $value) {
            $sql_client_meta_parts[] = "('$client_id', '$field', '$value')";
        }

        // Proceed with client_meta insertion
        if (!empty($sql_client_meta_parts)) {
            $sql_client_meta = "INSERT INTO client_meta (client_id, meta_field, meta_value) VALUES " . implode(", ", $sql_client_meta_parts);
            if (!$conn->query($sql_client_meta)) {
                // Error handling
                header('Location: error.php');
                exit;
            }
        }

        // Success: Redirect to the onboard page with the client_code
        header('Location: onboard.php?client_code=' . urlencode($client_code));
        exit;
    } else {
        // Error handling for failure to insert into client_list
        header('Location: error.php');
        exit;
    }
} else {
    // Redirect to registration page if the form hasn't been submitted
    header('Location: registration.php');
    exit;
}
?>
