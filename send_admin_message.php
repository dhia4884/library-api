<?php
header('Content-Type: application/json; charset=utf-8');
require 'config.php'; // تضمين ملف اتصال قاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $message = $_POST['message'];
    $borrow_request_id = $_POST['borrow_request_id'] ?? null; // استقبال معرف طلب الإعارة (يمكن أن يكون فارغًا)

    if (empty($user_id) || empty($message)) {
        echo json_encode(array("success" => false, "message" => "يرجى إدخال معرف المستخدم والرسالة."));
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO admin_messages (user_id, borrow_request_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $borrow_request_id, $message);

    if ($stmt->execute()) {
        echo json_encode(array("success" => true, "message" => "تم إرسال الرسالة بنجاح."));
    } else {
        echo json_encode(array("success" => false, "message" => "حدث خطأ أثناء إرسال الرسالة: " . $stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array("success" => false, "message" => "طريقة الطلب غير مسموح بها."));
}
?>