<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('./config.php'); // Ensure this file has the $conn variable for MySQLi connection

if (session_id() == '') {
    session_start();
}

require_once('inc/header.php');
require_once('inc/navigation.php');

// Check if the redirect function is already declared
if (!function_exists('redirect')) {
    // Define the redirect function
    function redirect($url) {
        header("Location: $url");
        exit();
    }
}

// Check if the invoice_id is set in the URL and is not empty
if (!isset($_GET['invoice_id']) || empty($_GET['invoice_id'])) {
    redirect('index.php');
}

// Securely fetch the invoice details from the database
$invoiceId = $conn->real_escape_string($_GET['invoice_id']);

// Using prepared statement to fetch invoice details
$stmt = $conn->prepare("SELECT * FROM invoice_list WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No invoice found with the provided ID, display an error message or redirect
    echo "<div class='alert alert-danger'>No invoice found. Please check the ID or contact support.</div>";
    // Alternatively, you can redirect to an error page or the index page
    // redirect('index.php');
} else {
    $invoiceDetails = $result->fetch_assoc();
    // Proceed to display the receipt details
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: 30px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            text-align: center;
            padding: 20px;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed sidebar-mini-md sidebar-mini-xs">
    <div class="wrapper">
        <?php require_once('inc/topBarNav.php'); ?>
        <?php require_once('inc/navigation.php'); ?>

        <div class="content-wrapper pt-3" style="min-height: 567.854px;">
            <section class="content">
                <div class="container-fluid">
                    <div class="container">
                        <div class="message">
                            <?php if (isset($invoiceDetails)): ?>
                                Payment for invoice <?php echo htmlspecialchars($invoiceDetails['invoice_code']); ?> has been received. We will update you on the next processes.
                            <?php endif; ?>
                        </div>
                        <a href="index.php" class="btn">Return to Services</a>
                    </div>
                </div>
            </section>
        </div>

        <?php require_once('inc/footer.php'); ?>
    </div>
</body>
</html>
