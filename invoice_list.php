<?php
require_once('./config.php');
session_start();

if (!isset($_SESSION['userdata']) || empty($_SESSION['userdata']['client_code'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}

$client_code = $_SESSION['userdata']['client_code'];

// Prepare statement to fetch client ID based on client_code
$clientInfoQuery = $conn->prepare("SELECT id FROM client_list WHERE client_code = ?");
$clientInfoQuery->bind_param("s", $client_code);
$clientInfoQuery->execute();
$clientInfoResult = $clientInfoQuery->get_result();

if ($clientInfo = $clientInfoResult->fetch_assoc()) {
    $client_id = $clientInfo['id']; // Use client ID to fetch invoices
} else {
    // Handle case where client_code does not match any records
    echo "<script>alert('Client not found.'); location.href='logout.php';</script>";
    exit;
}

// Prepare statement to fetch invoices for the logged-in client
$invoicesQuery = $conn->prepare("SELECT * FROM invoice_list WHERE client_id = ? ORDER BY date_created DESC");
$invoicesQuery->bind_param("i", $client_id);
$invoicesQuery->execute();
$invoicesResult = $invoicesQuery->get_result();

require_once('inc/header.php');
require_once('inc/navigation.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Invoices</title>
    <!-- Include your stylesheet here -->
    <style>
        
    html, body {
    height: 100%;
    margin: 0;
		}
		.wrapper {
		    display: flex;
		    flex-direction: column;
		    min-height: 100vh; /* Use viewport height (vh) instead of percentage */
		}
		.content-wrapper {
		    flex: 1; /* This ensures it expands */
		    /* Ensure there's padding or margin if your content is directly touching the edges */
		    padding: 20px;
		}
		footer {
		    /* Style your footer */
		}

    </style>
</head>
<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed sidebar-mini-md sidebar-mini-xs">
    <div class="wrapper">
        <?php require_once('inc/topBarNav.php'); ?>

        <div class="content-wrapper pt-3" style="min-height: 567.854px;">
            <section class="content">
                <div class="container-fluid">
                    <h2>Invoice List</h2>
                    <?php if ($invoicesResult->num_rows > 0): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice Code</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Phone Number</th>
                                    <th>Payment Code</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($invoice = $invoicesResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($invoice['invoice_code']); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['total_amount']); ?></td>
                                        <td><?php echo $invoice['status'] == 1 ? 'Paid' : 'Pending'; ?></td>
                                        <td><?php echo htmlspecialchars($invoice['phonenumber']); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['code']); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['date_created']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">No invoices found.</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

    </div>
     <footer class="footer">
        <?php require_once('inc/footer.php'); ?>
        </footer>
</body>

</html>
