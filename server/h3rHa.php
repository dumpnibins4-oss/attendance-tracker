<?php
include "db/conn.php";

try {
    $password = '41877';

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("
        INSERT INTO att_track_users (lastName, firstName, middleName, school, address, contactNumber, birthday, gender, department, employee_id, biometrics_id, gmail, position)
        VALUES ('Arganda', 'Teddy', 'Buyco', 'Pampanga State University', 'San Agustin Lubao, Pampanga', '09123456789', '2001-04-19', 'Male', 'IT Department', '2026-41877', '41877', 'teddyarganda415@gmail.com', 'OJT')
    ");
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Account created successfully!']);

    $stmt = $conn->prepare("
        INSERT INTO att_track_login_credentials (biometrics_id, password)
        VALUES ('41877', '$hashed_password')
    ");
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}