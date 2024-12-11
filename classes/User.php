<?php
require_once 'DB.php';

class User {
    private $conn;

    public function __construct() {
        $database = new DB();
        $this->conn = $database->connect();
    }

    public function register($username, $email, $password, $first_name, $last_name, $address, $phone) {
        try {
            $query = 'INSERT INTO Users (username, email, password, first_name, last_name, address, phone)
                      VALUES (:username, :email, :password, :first_name, :last_name, :address, :phone)';

            $stmt = $this->conn->prepare($query);

            // Hash the password
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // Sanitize inputs to prevent XSS
            $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            $first_name = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');
            $last_name = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');
            $address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
            $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');

            // Bind parameters
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hashed);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);

            // Execute the query
            if ($stmt->execute()) {
                return true;
            } else {
                error_log("Database Error: " . implode(" | ", $stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $query = 'SELECT * FROM Users WHERE email = :email';
            $stmt = $this->conn->prepare($query);

            // Sanitize input
            $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            } else {
                error_log("Login failed: Incorrect email or password.");
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }

    public function getUserCartId($user_id) {
        try {
            $query = 'SELECT cart_id FROM Cart WHERE user_id = :user_id';
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $cart = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart) {
                return $cart['cart_id'];
            } else {
                error_log("No cart found for user ID: $user_id");
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }

    public function getUserDetails($user_id) {
        try {
            $query = 'SELECT username, email, first_name, last_name, address, phone FROM Users WHERE user_id = :user_id';
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $user_details = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_details) {
                return $user_details;
            } else {
                error_log("No user details found for user ID: $user_id");
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }
}
