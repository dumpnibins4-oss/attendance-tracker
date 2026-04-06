<?php
    include("../db/conn.php");
    header("Content-Type: application/json");

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $biometrics_id = $_POST['biometricsID'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($biometrics_id) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Biometrics ID and password are required']);
            exit;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM att_track_users WHERE biometrics_id = ?");
            $stmt->execute([$biometrics_id]);
            $user = $stmt->fetch();

            if ($user) {
                $stmt = $conn->prepare("SELECT * FROM att_track_login_credentials WHERE biometrics_id = ?");
                $stmt->execute([$biometrics_id]);
                $login = $stmt->fetch();
                if (password_verify($password, $login['password'])) {
                    $stmt = $conn->prepare("SELECT * FROM att_track_restrictions WHERE biometrics_id = ?");
                    $stmt->execute([$biometrics_id]);
                    $restriction = $stmt->fetch();

                    if ($restriction) {
                        $_SESSION['restriction'] = $restriction;
                        $_SESSION['current_user'] = $user;
                        http_response_code(200);
                        echo json_encode(['success' => true, 'message' => 'Successfully signed in!']);
                    } else {
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => 'You do not have access in this system.']);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
                }
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'This user does not exist.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
?>