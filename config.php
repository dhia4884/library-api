<?php
/*$host = "localhost";
$user = "root"; // اسم المستخدم الافتراضي لـ MySQL
$password = ""; // اتركه فارغًا إذا لم تقم بتعيين كلمة مرور
$database = "library_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
} else {
}*/

// Define connection parameters
$host = "mainline.proxy.rlwy.net";
$user = "root";
$password = "trTvLHjUUtnsThFSRiwOykVbdkufGaLV";
$database = "library_db";

// Create a connection string
$connectionString = "mysql://root:trTvLHjUUtnsThFSRiwOykVbdkufGaLV@mainline.proxy.rlwy.net:27997/railway";

try {
    // Create connection using PDO with the connection string
    $conn = new PDO($connectionString, $user, $password);
    
    // Set error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: Uncomment the following line to show successful connection
    // echo "تم الاتصال بقاعدة البيانات بنجاح";
} catch(PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}


?>
