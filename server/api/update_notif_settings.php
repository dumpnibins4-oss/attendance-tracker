<?php
header("Content-Type: application/json");
include("../db/conn.php");

if (!isset($_SESSION['current_user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['current_user']['id'];
$input  = json_decode(file_get_contents('php://input'), true);

if (!isset($input['setting_id']) || !isset($input['is_on'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing setting_id or is_on']);
    exit;
}

$settingId = (int) $input['setting_id'];
$isOn      = $input['is_on'] ? 1 : 0;

try {
    // Only allow updating rows that belong to the current user
    $stmt = $conn->prepare(
        "UPDATE att_track_notif_setting SET is_on = ? WHERE id = ? AND user_id = ?"
    );
    $stmt->execute([$isOn, $settingId, $userId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Setting not found or access denied']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Notification preference saved', 'is_on' => (bool)$isOn]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
