<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/db.php';

// session_start() must be called before any HTML output, which is why it's in header.php
// But for the login process itself, let's ensure it's started if we need to set session vars.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$errors = [];

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // --- DEBUG: Check if we received the POST data ---
    // echo "Attempting login for username: " . htmlspecialchars($username) . "<br>";

    if (empty($username) || empty($password)) {
        $errors[] = 'Both username and password are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // --- DEBUG: User was found in the database ---
            // echo "User found. ID: " . $user['id'] . ".<br>";
            // echo "Stored Hash: " . htmlspecialchars($user['password']) . "<br>";
            
            // Now, verify the password
            if (password_verify($password, $user['password'])) {
                // --- DEBUG: Password is correct! ---
                // echo "Password verification successful!<br>";

                // Password is correct, start session
                session_regenerate_id(true); // Regenerate ID to prevent session fixation
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // --- DEBUG: Session variables are set. Now redirecting... ---
                // die("Session set. Redirecting to index.php..."); 
                
                header("Location: index.php");
                exit; // ALWAYS exit after a header redirect

            } else {
                // --- DEBUG: Password verification failed ---
                // echo "Password verification FAILED.<br>";
                $errors[] = 'Invalid username or password.';
            }
        } else {
            // --- DEBUG: User was not found ---
            // echo "No user found with that username.<br>";
            $errors[] = 'Invalid username or password.';
        }
    }
}

// --- If we reach here, login failed or hasn't been attempted yet. ---
// --- Now we include the HTML header. ---
include 'includes/header.php';
?>

<div class="auth-container">
    <h2>Login</h2>
    <p>Welcome back! Please login to your account.</p>

    <?php if (isset($_GET['registered'])): ?>
        <div class="success-box">
            <p>Registration successful! You can now log in.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="auth-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>