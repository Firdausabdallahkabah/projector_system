<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$projector_id = $data['projector_id'] ?? '';
$date = $data['date'] ?? '';
$start_time = $data['start_time'] ?? '';
$end_time = $data['end_time'] ?? '';
$purpose = trim($data['purpose'] ?? '');

// Basic validation
if (empty($projector_id) || empty($date) || empty($start_time) || empty($end_time) || empty($purpose)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$start_datetime_str = "$date $start_time";
$end_datetime_str = "$date $end_time";

// More validation
if (strtotime($start_datetime_str) >= strtotime($end_datetime_str)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'End time must be after start time.']);
    exit;
}

try {
    // *** CRITICAL: SERVER-SIDE CONFLICT CHECK ***
    $stmt = $pdo->prepare(
        "SELECT id FROM reservations 
         WHERE projector_id = ? 
         AND status = 'Confirmed'
         AND (
            (start_time < ? AND end_time > ?) OR -- Overlaps the start
            (start_time >= ? AND start_time < ?)    -- Starts within the new slot
         )"
    );
    $stmt->execute([$projector_id, $end_datetime_str, $start_datetime_str, $start_datetime_str, $end_datetime_str]);
    
    if ($stmt->fetch()) {
        http_response_code(409); // 409 Conflict
        echo json_encode(['success' => false, 'message' => 'This time slot is already booked for the selected projector.']);
        exit;
    }

    // If no conflict, insert the reservation
    $insert_stmt = $pdo->prepare(
        "INSERT INTO reservations (user_id, projector_id, purpose, start_time, end_time) 
         VALUES (?, ?, ?, ?, ?)"
    );
    
    $success = $insert_stmt->execute([
        $_SESSION['user_id'],
        $projector_id,
        $purpose,
        $start_datetime_str,
        $end_datetime_str
    ]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Reservation successful!']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to make reservation. Please try again.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

?>