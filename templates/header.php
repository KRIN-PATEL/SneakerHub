<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : 'Sneaker Store'; ?></title>
    <link rel="stylesheet" href="../public/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap">
    <style>
      
    </style>
</head>
<body>
    <header>
        <!-- Logo Section -->
        <div class="logo">
            <a href="index.php"><img src="../images/SneakerHouse.png" alt="Sneaker House Logo"></a>
        </div>

        <!-- Navigation -->
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id']): ?>
                    <li><a href="product.php">Products</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
</body>
</html>