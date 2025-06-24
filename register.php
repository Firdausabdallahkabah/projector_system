<?php
require_once 'includes/db.php';
$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters long.';

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already taken.';
        }
    }

    // If no errors, create user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            header("Location: login.php?registered=success");
            exit;
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
include 'includes/header.php';
?>

<div class="auth-container">
    <h2>Register</h2>
    <p>Create an account to manage reservations.</p>
    
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" class="auth-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" required value="<?php echo htmlspecialchars($username); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit" class="btn-primary">Register</button>
    </form>
    <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include 'includes/footer.php'; ?>