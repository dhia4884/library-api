<?php
header('Content-Type: application/json');
include 'config.php'; // تضمين ملف الاتصال بقاعدة البيانات


if (isset($_POST['id']) && isset($_POST['status'])) {
    $requestId = $_POST['id'];
    $newStatus = $_POST['status'];

    $stmt = $conn->prepare("UPDATE borrow_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $requestId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث حالة الطلب بنجاح"]);
    } else {
        echo json_encode(["success" => false, "error" => "خطأ في تحديث السجل: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "لم يتم إرسال معرف الطلب أو الحالة"]);
}

$conn->close();
?>