<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO contacts (name, email, phone, issue, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['issue'],
            $_POST['message'] ?? ''
        ]);

        if($result) {
            echo "success";
        } else {
            http_response_code(500);
            echo "error";
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo "DB Error: " . $e->getMessage();
    }
}
?>