<?php
if (!isset($_GET['order_id'])) {
    die('Order ID not specified.');
}

$order_id = $_GET['order_id'];
$invoice_filename = '../invoices/invoice_' . $order_id . '.pdf';

if (file_exists($invoice_filename)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice_' . $order_id . '.pdf"');
    readfile($invoice_filename);
    exit;
} else {
    die('Invoice file not found.');
}
?>