<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('./config.php');

if (session_id() == '') {
    session_start();
}

if (!isset($_SESSION['userdata']) || empty($_SESSION['userdata']['client_code']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['service_id'])) {
    echo "<script>alert('Invalid access.'); location.href='login.php';</script>";
    exit;
}

// Sanitize inputs
$serviceId = filter_input(INPUT_POST, 'service_id', FILTER_SANITIZE_NUMBER_INT);
$remarks = filter_input(INPUT_POST, 'remarks', FILTER_SANITIZE_STRING);
$clientId = $_SESSION['userdata']['id']; // Assuming this comes from a secure source as it's from the session

$invoiceDetails = [];
$services = [];
$message = '';

$serviceQuery = $conn->query("SELECT price FROM services_list WHERE id = '" . $conn->real_escape_string($serviceId) . "'");
if ($service = $serviceQuery->fetch_assoc()) {
    $servicePrice = $service['price'];
    $invoiceCode = bin2hex(random_bytes(8)); 
    $status = isset($_POST['payment_code']) ? '1' : '0'; 

    $insertInvoiceQuery = $conn->prepare("INSERT INTO invoice_list (invoice_code, client_id, total_amount, discount, tax, remarks, status, date_created, date_updated) VALUES (?, ?, ?, '0', '0', ?, ?, NOW(), NOW())");
    $insertInvoiceQuery->bind_param("ssdsd", $invoiceCode, $clientId, $servicePrice, $remarks, $status);
    
    if ($insertInvoiceQuery->execute()) {
        $invoiceId = $conn->insert_id;

        $insertServiceQuery = $conn->prepare("INSERT INTO invoice_services (invoice_id, service_id, price) VALUES (?, ?, ?)");
        $insertServiceQuery->bind_param("isi", $invoiceId, $serviceId, $servicePrice);
        
        if ($insertServiceQuery->execute()) {
            $message = "Service selected successfully!";
            
            $invoiceDetailsQuery = $conn->query("SELECT * FROM invoice_list WHERE id = '$invoiceId'");
            $invoiceDetails = $invoiceDetailsQuery->fetch_assoc();

            $servicesDetailsQuery = $conn->query("SELECT s.name, s.description, inv_srv.price FROM invoice_services AS inv_srv JOIN services_list AS s ON inv_srv.service_id = s.id WHERE inv_srv.invoice_id = '$invoiceId'");
            while ($row = $servicesDetailsQuery->fetch_assoc()) {
                $services[] = $row;
            }
        } else {
            $message = "Error linking service to invoice.";
        }
    } else {
        $message = "Error creating invoice.";
    }
} else {
    $message = "Service not found.";
}

require_once('inc/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Confirmation</title>
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
        .btn-pay {
            background-color: #28a745;
        }
        .btn-pay:hover {
            background-color: #218838;
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
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php if ($invoiceDetails && $services): ?>
            <h2>Invoice Details</h2>
            <p><strong>Invoice Code:</strong> <?php echo htmlspecialchars($invoiceDetails['invoice_code']); ?></p>
            <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($invoiceDetails['total_amount']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($invoiceDetails['status'] == '1' ? 'Paid' : 'Pending'); ?></p>
            <p><strong>Remarks:</strong> <?php echo htmlspecialchars($invoiceDetails['remarks']); ?></p>
            
            <h3>Services</h3>
            <?php foreach ($services as $service): ?>
                <p>
                    <strong>Name:</strong> <?php echo htmlspecialchars($service['name']); ?><br>
                    <strong>Description:</strong> <?php echo htmlspecialchars($service['description']); ?><br>
                    <strong>Price:</strong> <?php echo htmlspecialchars($service['price']); ?>
                </p>
            <?php endforeach; ?>
            <form action="verify_payment.php" method="post">
                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoiceId); ?>">
                <button type="submit" class="btn btn-pay">Pay Now</button>
            </form>
        <?php endif; ?>
        <a href="service_list.php" class="btn">Return to Services</a>
    </div>
                </div>
            </section>
        </div>




        <!-- Payment Modal -->

        <!-- Payment Modal -->
        <div id="paymentModal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4); padding-top: 60px;">
            <div class="container" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 40%;">
                <h2>Pay Bill Information</h2>
                <p>AIC Kijabe Hospital</p>
                <p>Paybill: 512900</p>
                <p>Account: Name of Service</p>
                <form id="paymentForm">
                    <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoiceId); ?>">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required><br><br>
                    <label for="code">Transaction Code:</label>
                    <input type="text" id="code" name="code" required><br><br>
                    <button type="button" class="btn btn-pay" onclick="verifyPayment()">Verify</button>
                </form>
                <span onclick="document.getElementById('paymentModal').style.display='none'" class="btn">Close</span>
            </div>
        </div>


        <!-- End Payment Modal -->
        <?php require_once('inc/footer.php'); ?>
    </div>

    <script>
// Function to show the modal
function showPaymentModal() {
    document.getElementById('paymentModal').style.display = 'block';
}

// Function to verify payment
function verifyPayment() {
    var formData = $('#paymentForm').serialize(); // Serialize form data
    $.ajax({
        type: 'POST',
        url: 'verify_payment.php', // PHP file to process the verification
        data: formData,
        success: function(response) {
            // If verification successful
            if(response == 'success') {
                window.location.href = 'receipt.php?invoice_id=' + $('input[name="invoice_id"]').val(); // Redirect to receipt page
            } else {
                alert('Verification failed. Please try again.');
            }
        }
    });
}

// Replace the Pay Now button form submission with showPaymentModal function
$('form[action="verify_payment.php"]').submit(function(e) {
    e.preventDefault(); // Prevent the default form submission
    showPaymentModal();
});
</script>

</body>
</html>
