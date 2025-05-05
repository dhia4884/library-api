<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Accept");

include 'config.php';

if (!$conn) {
    echo json_encode(["success" => false, "message" => "فشل الاتصال بقاعدة البيانات"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "أدخل بريدك الإلكتروني وكلمة المرور"]);
    exit;
}

$adminEmail = "admin@example.com";
$adminPassword = "123456";

if ($email == $adminEmail && $password == $adminPassword) {
    echo json_encode([
        "success" => true,
        "message" => "تم تسجيل دخول المسؤول بنجاح",
        "user" => [
            "id" => 0,
            "name" => "Admin",
            "email" => $adminEmail,
            "role" => "admin"
        ]
    ]);
    exit;
}

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "تم تسجيل الدخول بنجاح",
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => "user"
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "خطأ في إدخال كلمة المرور"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "خطأ في إدخال البريد الإلكتروني أو كلمة المرور"]);
}

$stmt->close();
$conn->close();
?>