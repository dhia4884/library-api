<?php
error_reporting(E_ALL); // إضافة تسجيل الأخطاء
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Accept");

include 'config.php';

if (!$conn) {
    echo json_encode(["message" => "فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error()]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'] ?? null;
$author = $data['author'] ?? null;
$image_url = $data['image_url'] ?? null;
$status = $data['status'] ?? 'available';

if (!$title || !$author) {
    echo json_encode(["message" => "يرجى إدخال عنوان الكتاب والمؤلف"]);
    exit;
}

$query = "INSERT INTO books (title, author, image_url, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $title, $author, $image_url, $status);

if ($stmt->execute()) {
    echo json_encode(["message" => "تمت إضافة الكتاب بنجاح"]);
} else {
    echo json_encode(["message" => "خطأ في إضافة الكتاب: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>