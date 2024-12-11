<?php
session_start();

require_once '../classes/Product.php';

$pageTitle = 'Products'; // Set the page title
include '../templates/header.php';

$product = new Product();

// Check if a category is selected and filter the products
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
if ($category_id) {
    $products = $product->getProductsByCategory($category_id);
} else {
    $products = $product->getAllProducts();
}

// Fetch all categories for the dropdown
$categories = $product->getCategories();
?>

<h1>Products</h1>

<div class="container py-5">
    <h1 class="text-center mb-4 text-primary">Our Products</h1>

    <!-- Category Filter -->
    <form method="POST" action="product.php" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <label for="category_id" class="form-label">Filter by Category:</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo (isset($category_id) && $category_id == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm product-card">
                    <img src="../<?php echo htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-dark"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-success fw-bold">Price: $<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="mb-3">
                                <label for="quantity-<?php echo $product['product_id']; ?>" class="form-label">Quantity:</label>
                                <input type="number" name="quantity" maxlength="15" id="quantity-<?php echo $product['product_id']; ?>" class="form-control" value="1" min="1" max="15">
                            </div>
                            <button type="submit" class="btn btn-success mt-auto w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    include '../templates/footer.php';
    ?>