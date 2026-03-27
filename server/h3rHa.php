<?php
// include "db/conn.php";

// try {
//     $password = 'megamindmalakiulo';

//     // Hash password
//     $hashed_password = password_hash($password, PASSWORD_BCRYPT);

//     $stmt = $conn->prepare("
//         INSERT INTO att_track_users (lastName, firstName, middleName, employee_id, biometrics_id, gmail, password, position)
//         VALUES ('Salenga', 'Vince Emanuelle', 'Aguirre', '2026-41882', '41882', 'vince.salenga@gmail.com', ?, 'OJT')
//     ");
//     $stmt->execute([$hashed_password]);
//     echo json_encode(['success' => true, 'message' => 'Account created successfully!']);

// }
// catch (Exception $e) {
//     echo json_encode(['success' => false, 'message' => $e->getMessage()]);
// }