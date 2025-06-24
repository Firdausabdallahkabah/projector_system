<?php
// We will assume only logged-in users can access this.
// For a real-world app, you'd add a check for an 'admin' role.
require_once 'includes/header.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php';

// --- Handle Form Submissions (Add/Update/Delete) ---
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- ADD A NEW PROJECTOR ---
    if (isset($_POST['add_projector'])) {
        $name = trim($_POST['name']);
        $room = trim($_POST['room']);
        $status = $_POST['status'];
        $description = trim($_POST['description']);
        if (!empty($name) && !empty($room)) {
            $stmt = $pdo->prepare("INSERT INTO projectors (name, room, status, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $room, $status, $description]);
            $message = "<div class='success-box'>Projector added successfully!</div>";
        } else {
            $message = "<div class='error-box'>Name and Room are required.</div>";
        }
    }

    // --- DELETE A PROJECTOR ---
    if (isset($_POST['delete_projector'])) {
        $id = $_POST['projector_id'];
        // Note: In a real app, check for existing reservations before deleting.
        $stmt = $pdo->prepare("DELETE FROM projectors WHERE id = ?");
        $stmt->execute([$id]);
        $message = "<div class='success-box'>Projector deleted successfully!</div>";
    }
}

// Fetch all projectors to display them
$stmt = $pdo->query("SELECT * FROM projectors ORDER BY name");
$projectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="view-container">
    <h2>Manage Projectors</h2>
    <?php echo $message; ?>

    <!-- ADD NEW PROJECTOR FORM -->
    <div class="form-container" style="margin-bottom: 3rem;">
        <h3>Add New Projector</h3>
        <form action="manage_projectors.php" method="POST">
            <div class="form-group">
                <label for="name">Projector Name (e.g., BenQ MW612)</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="room">Room / Location</label>
                <input type="text" name="room" id="room" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="Available">Available</option>
                    <option value="Under Maintenance">Under Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3"></textarea>
            </div>
            <button type="submit" name="add_projector" class="btn-primary">Add Projector</button>
        </form>
    </div>

    <!-- LIST OF EXISTING PROJECTORS -->
    <div class="table-container">
        <h3>Existing Projectors</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Room</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projectors as $projector): ?>
                <tr>
                    <td><?php echo htmlspecialchars($projector['name']); ?></td>
                    <td><?php echo htmlspecialchars($projector['room']); ?></td>
                    <td><span class="status <?php echo htmlspecialchars($projector['status']); ?>"><?php echo htmlspecialchars($projector['status']); ?></span></td>
                    <td>
                        <form action="manage_projectors.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this projector? This cannot be undone.');">
                            <input type="hidden" name="projector_id" value="<?php echo $projector['id']; ?>">
                            <button type="submit" name="delete_projector" class="btn-danger">Delete</button>
                        </form>
                        <!-- Note: An "Edit" button would lead to another form pre-filled with this projector's data. -->
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($projectors)): ?>
            <p>No projectors found. Add one using the form above.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>