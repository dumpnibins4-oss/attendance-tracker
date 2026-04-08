<?php
    include("../db/conn.php");
    header("Content-Type: application/json");

    if (!isset($_SESSION['current_user'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $userId = $_SESSION['current_user']['id'];
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {

        // ──────────────────────────────────────
        // TIME IN
        // ──────────────────────────────────────
        case 'time_in':
            try {
                // Check if already timed in today
                $stmt = $conn->prepare("
                    SELECT id FROM att_track_attendance
                    WHERE user_id = ? AND CAST(time_in AS DATE) = CAST(GETDATE() AS DATE)
                ");
                $stmt->execute([$userId]);

                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'You have already timed in today.']);
                    exit;
                }

                // Determine status in SQL Server (same clock as time_in) to avoid timezone mismatch
                $stmt = $conn->prepare("
                    INSERT INTO att_track_attendance (user_id, time_in, status)
                    VALUES (?, GETDATE(),
                        CASE WHEN DATEPART(HOUR, GETDATE()) > 8
                                  OR (DATEPART(HOUR, GETDATE()) = 8 AND DATEPART(MINUTE, GETDATE()) >= 30)
                             THEN 'late' ELSE 'present' END)
                ");
                $stmt->execute([$userId]);

                // Return the created record
                $stmt = $conn->prepare("
                    SELECT TOP 1 id, time_in, time_out, status, hours
                    FROM att_track_attendance
                    WHERE user_id = ? ORDER BY id DESC
                ");
                $stmt->execute([$userId]);
                $record = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode([
                    'success' => true,
                    'message' => 'Timed in successfully!',
                    'record'  => $record
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        // ──────────────────────────────────────
        // TIME OUT
        // ──────────────────────────────────────
        case 'time_out':
            try {
                // Get today's record (just need id and time_out check)
                $stmt = $conn->prepare("
                    SELECT id, time_out
                    FROM att_track_attendance
                    WHERE user_id = ? AND CAST(time_in AS DATE) = CAST(GETDATE() AS DATE)
                ");
                $stmt->execute([$userId]);
                $record = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$record) {
                    echo json_encode(['success' => false, 'message' => 'No time-in record found for today.']);
                    exit;
                }

                if ($record['time_out'] !== null) {
                    echo json_encode(['success' => false, 'message' => 'You have already timed out today.']);
                    exit;
                }

                // Lunch minutes sent from client (total minutes spent on lunch)
                $lunchMinutes = floatval($_POST['lunchMinutes'] ?? 0);

                // Calculate hours entirely in SQL Server to avoid timezone mismatches
                // Since time_in was set with GETDATE(), we compute diff on the same server clock
                $stmt = $conn->prepare("
                    UPDATE att_track_attendance
                    SET time_out = GETDATE(),
                        hours = ROUND(
                            CASE
                                WHEN (DATEDIFF(SECOND, time_in, GETDATE()) / 3600.0 - ? / 60.0) > 8.0 THEN 8.0
                                WHEN (DATEDIFF(SECOND, time_in, GETDATE()) / 3600.0 - ? / 60.0) < 0   THEN 0
                                ELSE (DATEDIFF(SECOND, time_in, GETDATE()) / 3600.0 - ? / 60.0)
                            END, 2)
                    WHERE id = ?
                ");
                $stmt->execute([$lunchMinutes, $lunchMinutes, $lunchMinutes, $record['id']]);

                // Fetch the computed hours back
                $stmt = $conn->prepare("SELECT hours FROM att_track_attendance WHERE id = ?");
                $stmt->execute([$record['id']]);
                $hours = floatval($stmt->fetchColumn());

                // Add to user's accumulated_hours
                $stmt = $conn->prepare("
                    UPDATE att_track_users
                    SET accumulated_hours = ISNULL(accumulated_hours, 0) + ?
                    WHERE id = ?
                ");
                $stmt->execute([$hours, $userId]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Timed out successfully!',
                    'hours'   => $hours
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        // ──────────────────────────────────────
        // GET TODAY'S RECORD
        // ──────────────────────────────────────
        case 'get_today':
            try {
                $stmt = $conn->prepare("
                    SELECT id, time_in, time_out, status, hours
                    FROM att_track_attendance
                    WHERE user_id = ? AND CAST(time_in AS DATE) = CAST(GETDATE() AS DATE)
                ");
                $stmt->execute([$userId]);
                $record = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode([
                    'success' => true,
                    'record'  => $record ?: null
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        // ──────────────────────────────────────
        // GET ALL RECORDS (for table)
        // ──────────────────────────────────────
        case 'get_records':
            try {
                $stmt = $conn->prepare("
                    SELECT id, time_in, time_out, status, hours, journal
                    FROM att_track_attendance
                    WHERE user_id = ?
                    ORDER BY time_in DESC
                ");
                $stmt->execute([$userId]);
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    'success' => true,
                    'records' => $records
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        // ──────────────────────────────────────
        // SAVE JOURNAL
        // ──────────────────────────────────────
        case 'save_journal':
            try {
                $recordId = $_POST['record_id'] ?? null;
                $journal  = trim($_POST['journal'] ?? '');

                if (!$recordId) {
                    echo json_encode(['success' => false, 'message' => 'Record ID is required.']);
                    exit;
                }

                if (strlen($journal) === 0) {
                    echo json_encode(['success' => false, 'message' => 'Journal entry cannot be empty.']);
                    exit;
                }

                // Make sure the record belongs to the user
                $stmt = $conn->prepare("
                    UPDATE att_track_attendance
                    SET journal = ?
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$journal, $recordId, $userId]);

                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Journal saved successfully!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save or record not found.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
?>
