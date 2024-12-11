<?php
session_start();

if (!isset($_GET['order_id'])) {
    die('Order ID not specified.');
}

$order_id = $_GET['order_id'];
$invoice_filename = '../invoices/invoice_' . $order_id . '.pdf';

$pageTitle = 'Order Success'; // Set the page title
include '../templates/header.php';
?>

<div class="container my-5">
    <div class="text-center">
        <h1 class="display-4 text-success">
            <i class="bi bi-check-circle"></i> Order Successful!
        </h1>
        <p class="lead">Your order has been placed successfully. Thank you for shopping with us!</p>

        <div class="gif-container">
            <img src="../images/gif.gif" alt="Order Success GIF" class="img-fluid gif-medium">
        </div>

    </div>

    <div class="card my-4">
        <div class="card-body">
            <h2 class="card-title">Order Summary</h2>
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Date:</strong> <?= date('F j, Y') ?></p>
        </div>
    </div>

    <div class="card my-4">
        <div class="card-body">
            <h2 class="card-title">Next Steps</h2>
            <p class="card-text">You will receive a confirmation email with your order details and tracking information once your order has been processed.</p>
        </div>
    </div>

    <div class="text-center">
        <a href="index.php" class="btn btn-secondary me-2">
            <i class="bi bi-house-door"></i> Return to Home
        </a>
        <a href="download_invoice.php?order_id=<?= $order_id ?>" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Download Invoice
        </a>
    </div>
</div>

<?php
include '../templates/footer.php';
?>