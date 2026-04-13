<?php
    include("../db/conn.php");
    header("Content-Type: application/json");

    try {
        $stmt = $conn->prepare("SELECT * FROM [LRNPH_HIK_BIO].[dbo].hik_logs_staging");
        $stmt->execute();
        $logs = $stmt->fetchAll();
        echo json_encode($logs);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
?>
