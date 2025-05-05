<?php
header('Content-Type: application/json; charset=utf-8');

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(array("name" => $row['name']));
    } else {
        echo json_encode(array("error" => "لم يتم العثور على المستخدم"));
    }

    $stmt->close();
} else {
    echo json_encode(array("error" => "طلب غير صالح"));
}

$conn->close();
?>