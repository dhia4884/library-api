<?php
header('Content-Type: application/json; charset=utf-8');
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT br.id, br.user_id, br.book_id, br.user_name, br.request_date, br.due_date, br.status, br.pdf_file_path,
                   b.title, b.image_url
            FROM borrow_requests br
            INNER JOIN books b ON br.book_id = b.id
            ORDER BY br.request_date DESC";
    $result = $conn->query($sql);

    $borrowRequests = array();
    while ($row = $result->fetch_assoc()) {
        $borrowRequests[] = $row;
    }

    echo json_encode(array("success" => true, "borrowRequests" => $borrowRequests));
} else {
    echo json_encode(array("success" => false, "error" => "Invalid request"));
}

$conn->close();
?>