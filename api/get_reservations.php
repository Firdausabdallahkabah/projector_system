<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$date_filter = $_GET['date'] ?? '';
$projector_filter = $_GET['projector_id'] ?? 'all';

$sql = "SELECT r.id, r.purpose, r.start_time, r.end_time, p.name as projector_name, p.room, u.username 
        FROM reservations r
        JOIN projectors p ON r.projector_id = p.id
        JOIN users u ON r.user_id = u.id
        WHERE r.status = 'Confirmed'";
$params = [];

if ($date_filter) {
    $sql .= " AND DATE(r.start_time) = ?";
    $params[] = $date_filter;
}

if ($projector_filter !== 'all' && is_numeric($projector_filter)) {
    $sql .= " AND r.projector_id = ?";
    $params[] = $projector_filter;
}

$sql .= " ORDER BY r.start_time ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reservations);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>