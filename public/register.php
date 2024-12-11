<?php
require_once '../classes/User.php';

$pageTitle = 'Register';
include '../templates/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if (empty($username) || empty($email) || empty($password)) {
        $message = '<div class="alert alert-danger">Username, email, and password are required.</div>';
    } else {
        $user = new User();
        if ($user->register($username, $email, $password, $first_name, $last_name, $address, $phone)) {
            $message = '<div class="alert alert-success">User registered successfully! <a href="login.php">Log in here</a>.</div>';
        } else {
            $message = '<div class="alert alert-danger">User registration failed! Check logs for details.</div>';
        }
    }
}
?>

<div class="registration-container py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="text-center text-primary mb-4">Register</h1>
                        <p class="text-center text-muted mb-4">Please fill out the form below to create a new account.</p>

                        <!-- Display message -->
                        <?php echo $message; ?>

                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name:</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name:</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address:</label>
                                <textarea name="address" id="address" class="form-control" placeholder="Address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone:</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone">
                            </div>
                            <div class="d-grid">
                                <input class="btn btn-primary btn-lg" type="submit" value="Register">
                            </div>
                        </form>

                        <p class="text-center mt-4">Already have an account? <a href="login.php">Log in here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../templates/footer.php';
?>
