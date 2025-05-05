<?php
header('Content-Type: application/json; charset=utf-8');
require 'config.php'; // تضمين ملف اتصال قاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];

    if (empty($request_id)) {
        echo json_encode(array("success" => false, "message" => "يرجى إدخال معرف الطلب."));
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM borrow_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(array("success" => true, "message" => "تم حذف الطلب بنجاح."));
        } else {
            echo json_encode(array("success" => false, "message" => "لم يتم العثور على طلب بهذا المعرف."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "حدث خطأ أثناء حذف الطلب: " . $stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array("success" => false, "message" => "طريقة الطلب غير مسموح بها."));
}
?>