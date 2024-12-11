<?php
session_start();
$pageTitle = 'Home';
include '../templates/header.php';

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'sneakers_db';

// Create a connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");

// Select the database
$conn->select_db($db_name);

// Create tables if they don't exist
$table_creation_queries = [
    "CREATE TABLE IF NOT EXISTS Users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        address TEXT,
        phone VARCHAR(15),
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS Categories (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        category_name VARCHAR(100) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Products (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        product_name VARCHAR(100) NOT NULL,
        category_id INT,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        stock_quantity INT DEFAULT 0,
        image_url VARCHAR(255),
        FOREIGN KEY (category_id) REFERENCES Categories(category_id)
    )",
    "CREATE TABLE IF NOT EXISTS Cart (
        cart_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES Users(user_id)
    )",
    "CREATE TABLE IF NOT EXISTS Cart_Items (
        cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
        cart_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price_at_purchase DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (cart_id) REFERENCES Cart(cart_id),
        FOREIGN KEY (product_id) REFERENCES Products(product_id)
    )",
    "CREATE TABLE IF NOT EXISTS orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(50) DEFAULT 'pending',
        FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS order_items (
        order_item_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_at_purchase DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS Invoices (
        invoice_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        invoice_pdf VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE
    )"
];

// Execute each table creation query
foreach ($table_creation_queries as $query) {
    if (!$conn->query($query)) {
        die("Error creating table: " . $conn->error);
    }
}

// Populate dummy data for Categories and Products if they are empty
$categories_check = $conn->query("SELECT COUNT(*) FROM Categories");
$products_check = $conn->query("SELECT COUNT(*) FROM Products");

if ($categories_check->fetch_row()[0] == 0 && $products_check->fetch_row()[0] == 0) {
    $conn->query("INSERT INTO Categories (category_name) VALUES
    ('Running Shoes'),
    ('Casual Sneakers'),
    ('Fashion Sneakers'),
    ('Training Shoes'),
    ('High-top Sneakers'),
    ");

    // $conn->query("INSERT INTO Products (product_name, category_id, price, description, stock_quantity, image_url) VALUES
    // ('Air Max 2024', 1, 210.99, 'Nike Air Max 2024, designed for comfort and style.', 100, 'images/air-max.png'),
    // ('Air Jordan 1 Mid SE', 2, 149.99, 'This AJ1? It\'s a showstopper. Glossy patent leather adds shine & luxe look.', 120, 'images/AJ1-Mid-SE.jpeg'),
    // ('AJ 6 Travis Scott Khaki', 3, 450, 'The Travis Scott x Air Jordan 6 Retro ‘British Khaki’ reunites Jordan Brand with the Houston rapper.', 90, 'images/TravisScottKhaki.jpeg')")
    // ;

    $conn->query("INSERT INTO Products (product_name, category_id, price, description, stock_quantity, image_url) VALUES
('Air Max 2024', 1, 210.99, 'Nike Air Max 2024, designed for comfort and style.', 100, 'images/air-max.png'),
('Air Jordan 1 Mid SE', 2, 149.99, 'This AJ1? It\'s a showstopper. Glossy patent leather adds shine & luxe look.', 120, 'images/AJ1-Mid-SE.jpeg'),
('Jordan MVP', 4, 215.99, 'The signature encapsulated Air-Sole units provide that always-satisfying lightweight and cushy feel, while the embroidered Jumpman logo adds the true-blue sporty flair.', 200, 'images/JORDAN+MVP.png'),
('Jordan True Flight', 5, 190, 'The Jordan True Flight is a lightweight and responsive game shoe, inspired by the play of the game\'s premier players.', 150, 'images/JORDAN+TRUE+FLIGHT.jpeg'),
('Air Force 07', 1, 150, 'A true sneaker royalty. The Nike Air Force 1 \'07 is back with crisp leather, bold colors, and classic Nike cutlines.', 50, 'images/NIKE+AIR+FORCE+1.png'),
('Nike Dunk Low Retro', 2, 150.49, 'Doing justice to its iconic predecessor, the Nike Dunk Low is everything your child needs when it comes to fashion-forward sneaker style.', 75, 'images/NIKE+DUNK+LOW+RETRO.png'),
('AJ 6 Travis Scott Khaki (British Khaki)', 3, 450, 'The Travis Scott x Air Jordan 6 Retro \'British Khaki\' reunites Jordan Brand with the Houston rapper.', 90, 'images/TravisScottKhaki.jpeg'),
('Reebok Classic Leather', 1, 69.99, 'Reebok Classic Leather sneakers, a simple yet versatile classic.', 180, 'images/W+BLAZER+MID.png'),
('Vans Old Skool', 8, 64.99, 'Vans Old Skool, a classic skateboarding sneaker with timeless appeal.', 160, 'images/W+NIKE+BLAZER+ROAM+MID.png')");
}

$conn->close();
?>
<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<main>
    <!-- Hero Section with Carousel -->
    <section class="hero">
        <div id="sneakerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="../images/banner5.jpg" class="d-block w-100" alt="Sneaker Banner 1">
                    <div class="carousel-caption d-none d-md-block">
                        <h2 class="text-uppercase overlay">Find Your Perfect Sneaker</h2>
                        <p>Explore a wide range of Sneakers that you love.</p>
                        <a href="product.php" class="btn btn-primary">Browse Sneaks</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="../images/bannerphp.jpg" class="d-block w-100" alt="Sneaker Banner 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h2 class="text-uppercase">Step Into Style</h2>
                        <p>Discover our latest collections of trendy sneakers.</p>
                        <a href="product.php" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="../images/banner6.jpg" class="d-block w-100" alt="Sneaker Banner 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h2 class="text-uppercase">Step Into Style</h2>
                        <p>Discover our latest collections of trendy sneakers.</p>
                        <a href="product.php" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>

            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#sneakerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#sneakerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    <!-- Featured Sneakers Section -->
    <section class="featured py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Featured Sneakers</h2>
            <div class="row">
                <!-- First Product -->
                <div class="col-md-6 mb-4">
                    <div class="card hover-card">
                        <img src="../images/JORDAN+MVP.png" class="card-img-top img-fluid" alt="Jordan MVP">
                        <div class="card-body text-center">
                            <h3 class="card-title">Jordan MVP</h3>
                            <p class="card-text">Hyped Retro's</p>
                            <a href="product.php" class="btn btn-primary hover-btn">More</a>
                        </div>
                    </div>
                </div>
                <!-- Second Product -->
                <div class="col-md-6 mb-4">
                    <div class="card hover-card">
                        <img src="../images/NikeDunkLow.png" class="card-img-top img-fluid" alt="Nike Dunk Low SE">
                        <div class="card-body text-center">
                            <h3 class="card-title">Nike Dunk Low SE</h3>
                            <p class="card-text">StreetWear and skateboarding</p>
                            <a href="product.php" class="btn btn-primary hover-btn">More</a>
                        </div>
                    </div>
                </div>
                <!-- Third Product -->
                <div class="col-md-6 mb-4">
                    <div class="card hover-card">
                        <img src="../images/TravisScottKhaki.jpeg" class="card-img-top img-fluid" alt="Travis Scott Khaki">
                        <div class="card-body text-center">
                            <h3 class="card-title">Travis Scott Khaki</h3>
                            <p class="card-text">Collaboration with Travis Scott</p>
                            <a href="product.php" class="btn btn-primary hover-btn">More</a>
                        </div>
                    </div>
                </div>
                <!-- Fourth Product -->
                <div class="col-md-6 mb-4">
                    <div class="card hover-card">
                        <img src="../images/air-max.png" class="card-img-top img-fluid" alt="Air Max 2024">
                        <div class="card-body text-center">
                            <h3 class="card-title">Air Max 2024</h3>
                            <p class="card-text">Designed for comfort and style</p>
                            <a href="product.php" class="btn btn-primary hover-btn">More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


</main>



<?php
include '../templates/footer.php';
?>