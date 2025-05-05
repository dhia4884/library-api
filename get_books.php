<?php
include 'config.php';

// جلب الكتب من قاعدة البيانات مع حقل التصنيف
$sql = "SELECT id, title, author, image_url, status, category FROM books";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $books = array();
    while ($row = $result->fetch_assoc()) {
        $books[] = array(
            "id" => $row["id"],
            "title" => $row["title"],
            "author" => $row["author"],
            "image" => $row["image_url"], 
            "status" => $row["status"],
            "category" => $row["category"] // إضافة التصنيف هنا
        );
    }
    echo json_encode(["success" => true, "books" => $books]);
} else {
    echo json_encode(["success" => false, "message" => "No books found"]);
}

$conn->close();
?>