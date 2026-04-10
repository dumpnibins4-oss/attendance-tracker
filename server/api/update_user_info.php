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

$user = $_SESSION['current_user'];
$bioId = $user['biometrics_id'];

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$contactNumber = isset($input['contactNumber']) ? trim($input['contactNumber']) : null;
$gmail = isset($input['gmail']) ? trim($input['gmail']) : null;

if ($contactNumber === null && $gmail === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No data provided to update']);
    exit;
}

try {
    $updateFields = [];
    $params = [];

    if ($contactNumber !== null) {
        $updateFields[] = "contactNumber = ?";
        $params[] = $contactNumber;
    }

    if ($gmail !== null) {
        $updateFields[] = "gmail = ?";
        $params[] = $gmail;
    }

    if (empty($updateFields)) {
        echo json_encode(['success' => true, 'message' => 'No changes made']);
        exit;
    }

    $params[] = $bioId;
    $sql = "UPDATE att_track_users SET " . implode(", ", $updateFields) . " WHERE biometrics_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Refresh session data
    $stmt = $conn->prepare("SELECT * FROM att_track_users WHERE biometrics_id = ?");
    $stmt->execute([$bioId]);
    $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($updatedUser) {
        $_SESSION['current_user'] = $updatedUser;
    }

    echo json_encode(['success' => true, 'message' => 'Information updated successfully', 'user' => $updatedUser]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
