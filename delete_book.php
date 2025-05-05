<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include 'config.php'; // تضمين ملف الاتصال بقاعدة البيانات

$data = json_decode(file_get_contents("php://input"), true);

$bookId = $data['id'];

if ($conn->connect_error) {
    echo json_encode(array('message' => 'فشل الاتصال بقاعدة البيانات'));
    exit();
}

$sql = "DELETE FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookId);

if ($stmt->execute()) {
    echo json_encode(array('message' => 'تم حذف الكتاب بنجاح'));
} else {
    echo json_encode(array('message' => 'فشل حذف الكتاب'));
}

$stmt->close();
$conn->close();
?>