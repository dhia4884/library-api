<?php
header('Content-Type: application/json; charset=utf-8');
require 'config.php'; // تضمين ملف اتصال قاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_GET['user_id'];

    if (empty($user_id)) {
        echo json_encode(array("success" => false, "message" => "يرجى إدخال معرف المستخدم."));
        exit();
    }

    $stmt = $conn->prepare("SELECT message FROM admin_messages WHERE user_id = ? ORDER BY sent_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = array();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row['message'];
    }

    echo json_encode(array("success" => true, "messages" => $messages));

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array("success" => false, "message" => "طريقة الطلب غير مسموح بها."));
}
?>