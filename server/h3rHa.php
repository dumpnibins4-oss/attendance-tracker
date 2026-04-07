<?php
include "db/conn.php";

try {
    $password = '41881';

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("
        INSERT INTO att_track_users (lastName, firstName, middleName, school, address, contactNumber, birthday, gender, department, employee_id, biometrics_id, gmail, position)
        VALUES ('Lingad', 'Tristan', 'Malayao', 'Pampanga State University', 'San Agustin Lubao, Pampanga', '09123456789', '2003-12-13', 'Male', 'IT Department', '2026-41881', '41881', 'tristanlingad03@gmail.com', 'OJT')
    ");
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Account created successfully!']);

    $stmt = $conn->prepare("
        INSERT INTO att_track_login_credentials (biometrics_id, password)
        VALUES ('41881', '$hashed_password')
    ");
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
}
catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}