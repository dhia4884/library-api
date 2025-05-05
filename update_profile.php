<?php
// Enable strict error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all requests
file_put_contents('update_profile_log.txt', date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("X-Content-Type-Options: nosniff");

// Function to validate and upload image
function uploadImage($file, $userId) {
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $fileName = "profile_" . $userId . "." . $imageFileType;
    $targetFile = $targetDir . $fileName;
    
    // Check if image file is a actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['error' => 'الملف ليس صورة صالحة'];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ['error' => 'حجم الصورة كبير جداً (الحد الأقصى 5MB)'];
    }
    
    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return ['error' => 'نوع الصورة غير مدعوم. يرجى استخدام JPG, JPEG, PNG أو GIF'];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ['success' => true, 'filename' => $fileName];
    } else {
        return ['error' => 'حدث خطأ أثناء تحميل الصورة'];
    }
}

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("طريقة الطلب غير مسموحة", 405);
    }

    // Log received data
    file_put_contents('update_profile_log.txt', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
    if (isset($_FILES['profile_image'])) {
        file_put_contents('update_profile_log.txt', "FILE data: " . print_r($_FILES['profile_image'], true) . "\n", FILE_APPEND);
    }

    // Get database connection
    include 'config.php';
    
    // Validate required fields
    $required = ['user_id', 'name', 'email'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("حقل مطلوب مفقود: $field", 400);
        }
    }

    $userId = (int)$_POST['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $birthDate = trim($_POST['birth_date'] ?? '');
    $gender = trim($_POST['gender'] ?? 'ذكر');

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("بريد إلكتروني غير صالح", 400);
    }

    // Check if email is already used by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("البريد الإلكتروني مستخدم بالفعل من قبل مستخدم آخر", 400);
    }

    $imageFileName = null;
    
    // Handle image upload if exists
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $imageResult = uploadImage($_FILES['profile_image'], $userId);
        if (isset($imageResult['error'])) {
            throw new Exception($imageResult['error'], 400);
        }
        $imageFileName = $imageResult['filename'];
    }

    // Update user data in database
    if ($imageFileName) {
        // Update with image
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, birth_date = ?, gender = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $birthDate, $gender, $imageFileName, $userId);
    } else {
        // Update without image
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, birth_date = ?, gender = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $birthDate, $gender, $userId);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("فشل في تحديث بيانات المستخدم: " . $conn->error, 500);
    }

    // Prepare success response
    $response = [
        'success' => true,
        'message' => 'تم تحديث الملف الشخصي بنجاح',
        'data' => [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'profile_image' => $imageFileName ? "https://0785-197-207-169-72.ngrok-free.app/uploads/$imageFileName" : null
        ]
    ];

    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Prepare error response
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode() ?: 500
    ];

    http_response_code($e->getCode() ?: 500);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    // Log error
    file_put_contents('update_profile_log.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    
    // Final log
    file_put_contents('update_profile_log.txt', "----------------------------------------\n", FILE_APPEND);
}
?>