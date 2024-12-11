<?php
session_start();
require_once '../classes/Order.php';
require_once '../classes/Cart.php';
require_once '../classes/User.php';
require_once '../libs/fpdf/fpdf.php';

if (!isset($_SESSION['cart_id']) || !isset($_SESSION['user_id'])) {
    die('You must log in to proceed with the checkout.');
}

$cart = new Cart();
$order = new Order();
$user = new User();

// Fetch user details from the database
$user_details = $user->getUserDetails($_SESSION['user_id']);
if (!$user_details) {
    die('User details not found.');
}

// Combine first and last names
$user_name = $user_details['first_name'] . ' ' . $user_details['last_name'];
$user_email = $user_details['email'];

// Get cart items and calculate the total amount
$cart_items = $cart->getCartItems($_SESSION['cart_id']);
if (!$cart_items) {
    die('Your cart is empty.');
}

$subtotal = array_sum(array_column($cart_items, 'price_at_purchase'));

// Define tax rate (e.g., 10%)
$tax_rate = 0.10;
$tax = $subtotal * $tax_rate;
$total_amount = $subtotal + $tax;

// Create a new order
$order_id = $order->createOrder($_SESSION['user_id'], $total_amount);

// Add items to the order
foreach ($cart_items as $item) {
    $order->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['price_at_purchase']);
}

// Generate PDF invoice
$pdf = new FPDF();
$pdf->AddPage();

// Add a centered medium-sized logo at the top
$logo_path = '../images/SneakerHouse.png'; // Update the path to your logo file
$pdf->Image($logo_path, 80, 10, 50); // Centered with x = 80 and width = 50
$pdf->Ln(40); // Add space below the logo

// Add user details on the left and date on the right
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, "User ID: {$_SESSION['user_id']}", 0, 0, 'L'); // Left-aligned User ID
$pdf->Cell(0, 10, "Date: " . date('Y-m-d'), 0, 1, 'R'); // Right-aligned Date

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, "Name: $user_name", 0, 0, 'L'); // Left-aligned User Name
$pdf->Ln(5); // Move to the next line
$pdf->Cell(100, 10, "Email: $user_email", 0, 0, 'L'); // Left-aligned User Email
$pdf->Ln(15); // Add space below user details

// Invoice Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C'); // Center align
$pdf->Ln(5); // Add some space

// Table Header with Blue Background
$pdf->SetFillColor(135, 206, 250); // Light blue
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(15, 10, 'S.No', 1, 0, 'C', true);
$pdf->Cell(85, 10, 'Product', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Price', 1, 1, 'C', true);

// Table Rows
$pdf->SetFont('Arial', '', 12);
$sn = 1;
foreach ($cart_items as $item) {
    $pdf->Cell(15, 10, $sn, 1);
    $pdf->Cell(85, 10, $item['product_name'], 1);
    $pdf->Cell(35, 10, $item['quantity'], 1);
    $pdf->Cell(45, 10, '$' . number_format($item['price_at_purchase'], 2), 1);
    $pdf->Ln();
    $sn++;
}

// Add totals to the PDF
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(135, 10, 'Subtotal', 0, 0, 'R');
$pdf->Cell(45, 10, '$' . number_format($subtotal, 2), 0, 1, 'R');

$pdf->Cell(135, 10, 'Tax (10%)', 0, 0, 'R');
$pdf->Cell(45, 10, '$' . number_format($tax, 2), 0, 1, 'R');

$pdf->Cell(135, 10, 'Total', 0, 0, 'R');
$pdf->Cell(45, 10, '$' . number_format($total_amount, 2), 0, 1, 'R');

// Add signature image and thank you message on the right side
$pdf->Ln(20); // Add some space before the footer
$signature_path = '../images/signature.png'; // Update the path to your signature image file
$pdf->Image($signature_path, 135, $pdf->GetY(), 40); // Right-aligned with x = 135 and width = 40

$pdf->Ln(30); // Add space after the signature
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Thank You for Your Purchase!', 0, 1, 'R'); // Right-aligned thank you message

// Output the PDF to a file
$invoice_filename = '../invoices/invoice_' . $order_id . '.pdf';
$pdf->Output('F', $invoice_filename);

// Save the invoice path to the database
$order->saveInvoice($order_id, $invoice_filename);

// Clear the user's cart
$cart->clearCartAndCartItems($_SESSION['cart_id']);

// Clear the cart_id from the session
unset($_SESSION['cart_id']);

// Redirect to order success page with order_id
header('Location: order_success.php?order_id=' . $order_id);
exit;
?>
