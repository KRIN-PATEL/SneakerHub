<!-- Footer -->
<footer class="custom-footer">
    <div class="container">
        <div class="row">
            <!-- Become an Insider -->
            <div class="col-md-4 footer-column">
                <h5>Become an Insider</h5>
                <p>Receive exclusive offers, sneaker news, and more when you subscribe.</p>
                <form class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Enter your email">
                    <button class="btn btn-primary" type="submit">Subscribe</button>
                </form>
                <div class="social-icons mt-3">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="col-md-4 footer-column">
                <h5>Quick Links</h5>
                <ul class="footer-nav">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id']): ?>
                        <li><a href="product.php">Products</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Our Story -->
            <div class="col-md-4 footer-column">
                <h5>Our Story</h5>
                <p>Discover the passion behind our sneakers and how we aim to deliver the best to you.</p>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="row mt-4 text-center">
            <div class="col">
                <p>&copy; <?php echo date('Y'); ?> Sneaker Store. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
