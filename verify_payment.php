<?php
require_once('./config.php');

if (!empty($_POST['invoice_id']) && !empty($_POST['phone']) && !empty($_POST['code'])) {
    $invoiceId = $conn->real_escape_string($_POST['invoice_id']);
    $phonenumber = $conn->real_escape_string($_POST['phone']);
    $code = $conn->real_escape_string($_POST['code']);

    // Update the invoice status to paid and set phone number and code
    $updateQuery = "UPDATE invoice_list SET status = '1', phonenumber = '$phonenumber', code = '$code' WHERE id = '$invoiceId'";
    if ($conn->query($updateQuery)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>
