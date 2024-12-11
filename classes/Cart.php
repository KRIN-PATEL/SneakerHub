<?php
require_once 'DB.php';

class Cart
{
    private $conn;

    public function __construct()
    {
        $database = new DB();
        $this->conn = $database->connect();
    }

    public function createCart($user_id)
    {
        if (!$user_id) {
            throw new Exception("Invalid user ID. Ensure the user is logged in.");
        }

        $query = 'INSERT INTO Cart (user_id) VALUES (:user_id)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        throw new Exception("Failed to create cart: " . implode(" | ", $stmt->errorInfo()));
    }

    public function addItemToCart($cart_id, $product_id, $quantity)
    {
        if (!$cart_id) {
            throw new Exception("Invalid cart ID. Ensure the cart exists.");
        }

        if (!$product_id || !$quantity || $quantity <= 0) {
            throw new Exception("Invalid product ID or quantity.");
        }

        $product_query = 'SELECT price FROM Products WHERE product_id = :product_id';
        $product_stmt = $this->conn->prepare($product_query);
        $product_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $product_stmt->execute();
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found with ID $product_id.");
        }

        $price_at_purchase = $product['price'] * $quantity;

        $query = 'INSERT INTO Cart_Items (cart_id, product_id, quantity, price_at_purchase)
                  VALUES (:cart_id, :product_id, :quantity, :price_at_purchase)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price_at_purchase', $price_at_purchase);

        if ($stmt->execute()) {
            return true;
        }
        throw new Exception("Failed to add item to cart: " . implode(" | ", $stmt->errorInfo()));
    }

    public function getCartItems($cart_id)
    {
        $query = 'SELECT ci.cart_item_id, ci.product_id, ci.quantity, ci.price_at_purchase, p.product_name, p.image_url
                  FROM Cart_Items ci
                  JOIN Products p ON ci.product_id = p.product_id
                  WHERE ci.cart_id = :cart_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeItemFromCart($cart_item_id)
    {
        $query = 'DELETE FROM Cart_Items WHERE cart_item_id = :cart_item_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_item_id', $cart_item_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function clearCartAndCartItems($cart_id)
    {
        try {
            // Start a transaction
            $this->conn->beginTransaction();

            // Delete cart items
            $deleteCartItemsQuery = 'DELETE FROM Cart_Items WHERE cart_id = :cart_id';
            $deleteCartItemsStmt = $this->conn->prepare($deleteCartItemsQuery);
            $deleteCartItemsStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $deleteCartItemsStmt->execute();

            // Delete the cart itself
            $deleteCartQuery = 'DELETE FROM Cart WHERE cart_id = :cart_id';
            $deleteCartStmt = $this->conn->prepare($deleteCartQuery);
            $deleteCartStmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $deleteCartStmt->execute();

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction if something went wrong
            $this->conn->rollBack();
            throw new Exception("Failed to clear cart: " . $e->getMessage());
        }
    }
}
