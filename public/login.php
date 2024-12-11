<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Cart.php';

$pageTitle = 'Login'; 
include '../templates/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $user = new User();
    $user_data = $user->login($email, $password);

    if ($user_data) {
        $_SESSION['user_id'] = $user_data['user_id'];

        $cart = new Cart();
        $cart_id = $user->getUserCartId($_SESSION['user_id']);

        // If no cart exists, create one
        if (!$cart_id) {
            $cart_id = $cart->createCart($_SESSION['user_id']);
        }

        $_SESSION['cart_id'] = $cart_id;

        header('Location: index.php');
        exit;
    } else {
        $error_message = "Invalid email or password!";
    }
}
?>

<main>
    <section class="login-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Login</h2>
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="login.php">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="d-grid">
                                    <input type="submit" value="Login" class="btn btn-primary">
                                </div>
                            </form>
                            <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
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
