<?php
header('Content-Type: application/json; charset=utf-8');
require 'config.php';

$response = array("success" => false, "message" => "");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response["success"] = true;
            $response["message"] = "تم حذف المستخدم بنجاح.";
        } else {
            $response["message"] = "لم يتم العثور على المستخدم.";
        }
    } else {
        $response["error"] = $stmt->error;
    }

    $stmt->close();
} else {
    $response["message"] = "طلب غير صالح.";
}

echo json_encode($response);

$conn->close();
?>