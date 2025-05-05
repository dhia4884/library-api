<?php
header('Content-Type: application/json');
include 'config.php'; // تضمين ملف الاتصال بقاعدة البيانات

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

if (isset($_POST['id']) && isset($_FILES['pdf_file'])) {
    $requestId = $_POST['id'];
    $file = $_FILES['pdf_file'];

    // التحقق من عدم وجود أخطاء في الرفع
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];

        // يمكنك هنا إضافة المزيد من التحقق من نوع وحجم الملف إذا لزم الأمر

        $uploadDirectory = 'uploads/'; // قم بإنشاء هذا المجلد على الخادم الخاص بك
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $newFileName = uniqid() . '_' . $fileName; // إنشاء اسم ملف فريد
        $destinationPath = $uploadDirectory . $newFileName;
        $uploadUrlBase = "https://3be4-41-105-161-44.ngrok-free.app/library_api/uploads/"; // استبدل بعنوان URL الأساسي لمجلد التحميل الخاص بك
        $destinationUrl = $uploadUrlBase . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            // تحديث مسار الملف (الآن URL) في قاعدة البيانات
            $stmt = $conn->prepare("UPDATE borrow_requests SET pdf_file_path = ? WHERE id = ?");
            $stmt->bind_param("si", $destinationUrl, $requestId);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "تم رفع ملف PDF بنجاح"]);
            } else {
                echo json_encode(["success" => false, "error" => "خطأ في تحديث قاعدة البيانات: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "error" => "فشل في نقل الملف المرفوع"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "حدث خطأ أثناء رفع الملف: " . $file['error']]);
    }
} else {
    echo json_encode(["success" => false, "error" => "لم يتم إرسال معرف الطلب أو ملف PDF"]);
}

$conn->close();
?>