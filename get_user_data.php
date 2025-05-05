<?php
header('Content-Type: application/json; charset=utf-8');

include 'config.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $sql = "SELECT id, name, email, phone, birth_date, gender, profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(array("error" => "لم يتم العثور على مستخدم بهذا المعرف."));
    }

    $stmt->close();
} else {
    echo json_encode(array("error" => "لم يتم توفير معرف المستخدم."));
}

$conn->close();
?>