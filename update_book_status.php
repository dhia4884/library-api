<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'config.php'; // تضمين ملف config.php

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id']) && isset($data['status'])) {
    $bookId = $data['id'];
    $newStatus = $data['status'];

    if ($conn->connect_error) {
        echo json_encode(['message' => 'فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error]);
        exit();
    }

    $sql = "UPDATE books SET status = '$newStatus' WHERE id = $bookId";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'تم تحديث حالة الكتاب بنجاح']);
    } else {
        echo json_encode(['message' => 'خطأ في تحديث حالة الكتاب: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['message' => 'بيانات غير كافية']);
}
?>