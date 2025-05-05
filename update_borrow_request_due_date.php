<?php
header('Content-Type: application/json');
include 'config.php'; // تضمين ملف الاتصال بقاعدة البيانات


if (isset($_POST['id']) && isset($_POST['dueDate'])) {
    $requestId = $_POST['id'];
    $newDueDate = $_POST['dueDate'];

    $stmt = $conn->prepare("UPDATE borrow_requests SET due_date = ? WHERE id = ?");
    $stmt->bind_param("si", $newDueDate, $requestId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث تاريخ الإرجاع بنجاح"]);
    } else {
        echo json_encode(["success" => false, "error" => "خطأ في تحديث السجل: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "لم يتم إرسال معرف الطلب أو تاريخ الإرجاع"]);
}

$conn->close();
?>