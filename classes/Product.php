<?php
require_once 'DB.php';

class Product {
    private $conn;

    public function __construct() {
        $this->conn = (new DB())->connect();
    }

    public function getAllProducts() {
        return $this->fetchProducts('SELECT * FROM Products');
    }

    public function getProductById($product_id) {
        return $this->fetchProducts('SELECT * FROM Products WHERE product_id = :product_id', [':product_id' => $product_id]);
    }

    public function getProductsByCategory($category_id) {
        return $this->fetchProducts('SELECT * FROM Products WHERE category_id = :category_id', [':category_id' => $category_id]);
    }

    public function getCategories() {
        return $this->fetchProducts('SELECT * FROM Categories');
    }

    public function addProduct($product_name, $category_id, $price, $description, $stock_quantity, $image_url) {
        $query = 'INSERT INTO Products (product_name, category_id, price, description, stock_quantity, image_url)
                  VALUES (:product_name, :category_id, :price, :description, :stock_quantity, :image_url)';
        
        return $this->executeQuery($query, [
            ':product_name' => $product_name,
            ':category_id' => $category_id,
            ':price' => $price,
            ':description' => $description,
            ':stock_quantity' => $stock_quantity,
            ':image_url' => $image_url
        ]);
    }

    private function fetchProducts($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function executeQuery($query, $params) {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
}

?>
