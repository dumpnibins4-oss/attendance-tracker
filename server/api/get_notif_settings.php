<?php
header("Content-Type: application/json");
include("../db/conn.php");

if (!isset($_SESSION['current_user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['current_user']['id'];

try {
    // Ensure all notif types have a setting row for this user (auto-seed defaults)
    $defaultOn = ['Late Alerts', 'Journal Reminders'];

    $allTypes = $conn->query("SELECT id, notif_type FROM att_track_notif_type")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allTypes as $type) {
        $check = $conn->prepare("SELECT id FROM att_track_notif_setting WHERE notif_type_id = ? AND user_id = ?");
        $check->execute([$type['id'], $userId]);
        if (!$check->fetch()) {
            $defaultVal = in_array($type['notif_type'], $defaultOn) ? 1 : 0;
            $ins = $conn->prepare("INSERT INTO att_track_notif_setting (notif_type_id, is_on, user_id) VALUES (?, ?, ?)");
            $ins->execute([$type['id'], $defaultVal, $userId]);
        }
    }

    // Fetch all settings with their labels/descriptions
    $stmt = $conn->prepare("
        SELECT s.id, s.notif_type_id, s.is_on, t.notif_type
        FROM att_track_notif_setting s
        JOIN att_track_notif_type t ON t.id = s.notif_type_id
        WHERE s.user_id = ?
        ORDER BY t.id ASC
    ");
    $stmt->execute([$userId]);
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'settings' => $settings]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
