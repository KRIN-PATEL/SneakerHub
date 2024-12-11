<?php
session_start();
require_once '../classes/Cart.php';
require_once '../classes/Product.php';

$pageTitle = 'Cart';
include '../templates/header.php';

$cart = new Cart();
$product = new Product();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

        if (!isset($_SESSION['cart_id'])) {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User is not logged in.");
            }
            $_SESSION['cart_id'] = $cart->createCart($_SESSION['user_id']);
        }

        if (isset($_POST['action']) && $_POST['action'] === 'remove') {
            $cart_item_id = filter_input(INPUT_POST, 'cart_item_id', FILTER_SANITIZE_NUMBER_INT);
            if ($cart_item_id && $cart->removeItemFromCart($cart_item_id)) {
                header('Location: cart.php');
                exit;
            } else {
                echo '<div class="alert alert-danger">Failed to remove item from cart.</div>';
            }
        } elseif ($product_id && $quantity > 0) {
            if ($cart->addItemToCart($_SESSION['cart_id'], $product_id, $quantity)) {
                header('Location: cart.php');
                exit;
            } else {
                echo '<div class="alert alert-danger">Failed to add item to cart.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Invalid product or quantity.</div>';
        }
    }

    if (isset($_SESSION['cart_id'])) {
        $cart_items = $cart->getCartItems($_SESSION['cart_id']);

        if ($cart_items) {
            echo '<div class="container my-5">';
            echo '<h2 class="text-center mb-4">Your Cart</h2>';
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered align-middle">';
            echo '<thead class="table-light">';
            echo '<tr>';
            echo '<th>Image</th>';
            echo '<th>Product Name</th>';
            echo '<th>Quantity</th>';
            echo '<th>Price</th>';
            echo '<th>Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($cart_items as $item) {
                echo '<tr>';
                echo '<td><img src="../' . htmlspecialchars($item['image_url']) . '" class="img-thumbnail" style="width: 80px;" alt="' . htmlspecialchars($item['product_name']) . '"></td>';
                echo '<td>' . htmlspecialchars($item['product_name']) . '</td>';
                echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
                echo '<td>$' . htmlspecialchars($item['price_at_purchase']) . '</td>';
                echo '<td>';
                echo '<form method="POST" style="display: inline-block;">';
                echo '<input type="hidden" name="action" value="remove">';
                echo '<input type="hidden" name="cart_item_id" value="' . htmlspecialchars($item['cart_item_id']) . '">';
                echo '<button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Remove</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            echo '<div class="text-end">';
            echo '<a href="checkout.php" class="btn btn-primary"><i class="bi bi-cart-check"></i> Click to Buy!!</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="container my-5">';
            echo '<h2 class="text-center text-muted">Your cart is empty.</h2>';
            echo '</div>';
        }
    } else {
        echo '<div class="container my-5">';
        echo '<h2 class="text-center text-muted">Your cart is empty.</h2>';
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<div class="container my-5">';
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '</div>';
}

include '../templates/footer.php';
?>
