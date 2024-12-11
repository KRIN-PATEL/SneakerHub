<?php
require_once 'DB.php';

class Order {
    private $conn;

    public function __construct() {
        $database = new DB();
        $this->conn = $database->connect();
    }

    public function createOrder($user_id, $total_amount) {
        $query = 'INSERT INTO Orders (user_id, total_amount) VALUES (:user_id, :total_amount)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total_amount', $total_amount);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderItem($order_id, $product_id, $quantity, $price_at_purchase) {
        $query = 'INSERT INTO Order_Items (order_id, product_id, quantity, price_at_purchase)
                  VALUES (:order_id, :product_id, :quantity, :price_at_purchase)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price_at_purchase', $price_at_purchase);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getOrderItems($order_id) {
        $query = 'SELECT * FROM Order_Items WHERE order_id = :order_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

	public function saveInvoice($order_id, $invoice_filename) {
        $query = 'INSERT INTO Invoices (order_id, invoice_pdf) VALUES (:order_id, :invoice_pdf)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':invoice_pdf', $invoice_filename, PDO::PARAM_STR);
        $stmt->execute();
    }
}
?>