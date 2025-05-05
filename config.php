<?php
$host = "localhost";
$user = "root"; // اسم المستخدم الافتراضي لـ MySQL
$password = ""; // اتركه فارغًا إذا لم تقم بتعيين كلمة مرور
$database = "library_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
} else {
}

?>
