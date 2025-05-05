<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include 'config.php'; // تضمين ملف الاتصال بقاعدة البيانات

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$password = $data['password'];

if ($conn->connect_error) {
    echo json_encode(array('success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات'));
    exit();
}

// تشفير كلمة المرور باستخدام password_hash()
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashedPassword); // استخدام كلمة المرور المشفرة

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'تم تسجيل المستخدم بنجاح'));
} else {
    echo json_encode(array('success' => false, 'message' => 'فشل تسجيل المستخدم'));
}

$stmt->close();
$conn->close();
?>