<?php
session_start();
include_once(__DIR__ . '/../db/conn.php');

header("Content-Type: application/json");

$userId = $_SESSION['current_user']['id'] ?? 0;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$attId = $data['att_id'] ?? 0;
$journal = $data['journal'] ?? '';

if ($attId) {
    try {
        $stmt = $conn->prepare("UPDATE att_track_attendance SET journal = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$journal, $attId, $userId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'DB error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
}
