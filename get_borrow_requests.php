<?php
header('Content-Type: application/json; charset=utf-8');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "SELECT
                br.id,
                br.user_id,
                br.book_id,
                br.user_name,
                br.request_date,
                br.due_date,
                br.status,
                br.pdf_file_path,
                b.title,
                b.image_url,
                am.message AS admin_message
            FROM
                borrow_requests br
            INNER JOIN
                books b ON br.book_id = b.id
            LEFT JOIN
                admin_messages am ON br.id = am.borrow_request_id
            WHERE
                br.user_id = ?
            ORDER BY
                am.sent_at DESC"; // ترتيب الرسائل (قد تحتاج تعديل حسب الأهمية)
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $borrowRequests = array();
    while ($row = $result->fetch_assoc()) {
        $borrowRequests[] = $row;
    }

    echo json_encode(array("success" => true, "borrowRequests" => $borrowRequests));

    $stmt->close();
} else {
    echo json_encode(array("success" => false, "error" => "Invalid request"));
}

$conn->close();
?>