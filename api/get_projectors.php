<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get all projectors
    $stmt = $pdo->query("SELECT id, name, room, status, description FROM projectors ORDER BY name");
    $projectors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each projector, check if it's currently reserved
    $now = new DateTime();
    $nowStr = $now->format('Y-m-d H:i:s');
    
    foreach ($projectors as &$projector) {
        $resStmt = $pdo->prepare("SELECT id FROM reservations WHERE projector_id = ? AND start_time <= ? AND end_time > ? AND status = 'Confirmed'");
        $resStmt->execute([$projector['id'], $nowStr, $nowStr]);
        $projector['current_status'] = $resStmt->fetch() ? 'Reserved' : 'Available';
    }

    echo json_encode($projectors);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>