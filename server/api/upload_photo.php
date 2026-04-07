<?php
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['current_user'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
        exit;
    }

    $file      = $_FILES['photo'];
    $maxSize   = 5 * 1024 * 1024; // 5 MB
    $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    // Validate size
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File is too large. Maximum allowed size is 5 MB.']);
        exit;
    }

    // Validate MIME type using finfo (more secure than $_FILES['type'])
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, $allowed)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, WebP, and GIF are allowed.']);
        exit;
    }

    // Build save path  →  client/public/assets/photos/<biometrics_id>.<ext>
    $ext         = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    };
    $biometricsId = $_SESSION['current_user']['biometrics_id'];
    $uploadDir    = __DIR__ . '/../../client/public/assets/photos/';
    $filename     = $biometricsId . '.' . $ext;
    $savePath     = $uploadDir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Remove any old photos for this user (different extensions)
    foreach (glob($uploadDir . $biometricsId . '.*') as $old) {
        unlink($old);
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $savePath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save the photo. Please try again.']);
        exit;
    }

    // Update session so the rest of the app reflects the change immediately
    $_SESSION['current_user']['profile_photo'] = './public/assets/photos/' . $filename;

    echo json_encode([
        'success'   => true,
        'message'   => 'Photo updated successfully!',
        'photo_url' => './public/assets/photos/' . $filename . '?t=' . time(),
    ]);
?>
