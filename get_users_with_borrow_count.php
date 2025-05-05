<?php
header('Content-Type: application/json; charset=utf-8');
require 'config.php';

$response = array("success" => false, "users" => array());

$sql = "SELECT
            u.id,
            u.name,
            CAST(COUNT(br.user_id) AS UNSIGNED) AS borrow_count
        FROM
            users u
        LEFT JOIN
            borrow_requests br ON u.id = br.user_id
        GROUP BY
            u.id
        ORDER BY
            u.name";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        $response["success"] = true;
        while ($row = $result->fetch_assoc()) {
            $response["users"][] = $row;
        }
    } else {
        $response["message"] = "لا يوجد مستخدمون.";
    }
} else {
    $response["error"] = $conn->error;
}

echo json_encode($response);

$conn->close();
?>