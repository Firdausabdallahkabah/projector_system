<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projector Reservation System</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <h1>Projector Management</h1>
        </div>
        <ul class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="index.php" data-view="dashboard">Dashboard</a></li>
                <li><a href="index.php?view=reserve" data-view="reserve">Make Reservation</a></li> <!-- Corrected link -->
                <li><a href="index.php?view=schedule" data-view="schedule">Schedule</a></li> <!-- Corrected link -->
                <li><a href="manage_projectors.php">Manage Projectors</a></li> <!-- ADD THIS LINE -->
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>