<?php
// Protect the page, redirect to login if not logged in
require_once 'includes/header.php'; // Session is started in header
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<div class="view-container">
    <div class="dashboard-view" id="dashboard">
        <h2>Available Projectors</h2>
        <div class="projector-grid">
            <!-- Projector cards will be loaded here by script.js -->
            <p>Loading projectors...</p>
        </div>
    </div>

    <div class="reserve-view hidden" id="reserve">
        <h2>Make a Reservation</h2>
        <form id="reservation-form">
            <div class="form-group">
                <label for="projector">Select Projector:</label>
                <select id="projector" name="projector_id" required>
                    <!-- Options will be loaded here by script.js -->
                </select>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="start-time">Start Time:</label>
                <input type="time" id="start-time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end-time">End Time:</label>
                <input type="time" id="end-time" name="end_time" required>
            </div>
            <div class="form-group">
                <label for="purpose">Purpose:</label>
                <textarea id="purpose" name="purpose" required placeholder="E.g., Team meeting, Client presentation"></textarea>
            </div>
            <button type="submit" class="btn-primary">Submit Reservation</button>
        </form>
    </div>

    <div class="schedule-view hidden" id="schedule">
        <h2>Reservation Schedule</h2>
        <div class="schedule-container">
            <div class="schedule-filters">
                <input type="date" id="schedule-date" class="schedule-date">
                <select id="schedule-projector" class="schedule-projector">
                    <option value="all">All Projectors</option>
                    <!-- Options will be loaded here by script.js -->
                </select>
            </div>
            <div class="schedule-grid">
                <!-- Schedule content will be loaded here by script.js -->
                <p>Select a date to view reservations.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>