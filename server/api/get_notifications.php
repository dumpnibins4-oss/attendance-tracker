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
    $stmt = $conn->prepare("
        SELECT id, title, content, created_at
        FROM att_track_notif
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format created_at as relative time
    foreach ($notifs as &$n) {
        $ts = strtotime($n['created_at']);
        $diff = time() - $ts;
        if ($diff < 60)             $n['time_ago'] = 'Just now';
        elseif ($diff < 3600)       $n['time_ago'] = floor($diff / 60) . 'm ago';
        elseif ($diff < 86400)      $n['time_ago'] = floor($diff / 3600) . 'h ago';
        elseif ($diff < 604800)     $n['time_ago'] = floor($diff / 86400) . 'd ago';
        else                        $n['time_ago'] = date('M j', $ts);
    }
    unset($n);

    echo json_encode([
        'success' => true,
        'count'   => count($notifs),
        'notifs'  => $notifs
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
