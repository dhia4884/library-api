<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['error' => 'معرف المستخدم مطلوب']);
    exit;
}

$stmt = $conn->prepare("SELECT name, email, phone, birth_date, gender, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // تحويل تاريخ الميلاد إلى تنسيق YYYY-MM-DD إذا كان موجوداً
    if ($user['birth_date']) {
        $user['birth_date'] = date('Y-m-d', strtotime($user['birth_date']));
    }
    
    echo json_encode([
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'birth_date' => $user['birth_date'],
        'gender' => $user['gender'],
        'profile_image' => $user['profile_image'] ? 'https://penguin-feasible-ideally.ngrok-free.app/uploads/' . $user['profile_image'] : null
    ]);
} else {
    echo json_encode(['error' => 'المستخدم غير موجود']);
}

$stmt->close();
$conn->close();
?>