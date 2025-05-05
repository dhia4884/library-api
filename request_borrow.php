<?php
header('Content-Type: application/json; charset=utf-8');


// إنشاء اتصال
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];

    // جلب اسم المستخدم من قاعدة البيانات
    $sql_user = "SELECT name FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();
        $user_name = $row_user['name'];

        // حساب تاريخ الاستحقاق بعد أسبوعين
        $request_date = date("Y-m-d H:i:s");
        $due_date = date('Y-m-d H:i:s', strtotime('+2 weeks', strtotime($request_date)));

        // إدراج طلب الإعارة في قاعدة البيانات
        $sql_insert = "INSERT INTO borrow_requests (user_id, book_id, user_name, due_date) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiss", $user_id, $book_id, $user_name, $due_date);

        if ($stmt_insert->execute()) {
            echo json_encode(array("message" => "تم إرسال طلب الإعارة بنجاح"));
        } else {
            echo json_encode(array("error" => "حدث خطأ أثناء حفظ طلب الإعارة: " . $stmt_insert->error));
        }

        $stmt_insert->close();
    } else {
        echo json_encode(array("error" => "لم يتم العثور على المستخدم"));
    }

    $stmt_user->close();
} else {
    echo json_encode(array("error" => "طريقة الطلب غير مسموح بها"));
}

$conn->close();
?>