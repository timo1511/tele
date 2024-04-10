
<?php
// Include the head.php file
include_once 'inc/head.php';

$fullname = "Unknown"; // Default value in case the client code is not found or not set

// Check if the client details are passed via GET parameters
if(isset($_GET['client_code'])) {
    // Sanitize the client_code to ensure it's a string without HTML tags
    $client_code = filter_input(INPUT_GET, 'client_code', FILTER_SANITIZE_STRING);

    // Fetch client details from the database using the client code
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT fullname FROM client_list WHERE client_code = ?");
    $stmt->bind_param("s", $client_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullname = $row['fullname'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #159ed5; /* Changed to theme color */
            color: #fff;
            text-align: center;
        }
        .card-body {
            padding: 30px;
        }
        .card-title {
            font-size: 24px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Registration Successful</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Client Name:</strong> <?php echo $fullname; ?></p>
                        <p><strong>Client Code (Username):</strong> <?php echo $client_code; ?></p>
                        <!-- Add other client details here -->
                        <p>Your registration was successful. Please verify your details above.</p>
                        <a href="login.php" class="btn btn-primary btn-block">Proceed to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
