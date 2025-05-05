<?php
include 'config.php';

$email = 'ali@example.com'; // ضع نفس البريد الذي جربت به
$password = '123456'; // ضع نفس كلمة المرور التي جربت بها

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        echo "✅ كلمة المرور صحيحة!";
    } else {
        echo "❌ كلمة المرور غير صحيحة!";
    }
} else {
    echo "❌ المستخدم غير موجود!";
}

$stmt->close();
?>
