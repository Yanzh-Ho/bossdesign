<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT * FROM faqs ORDER BY created_at DESC"); // 最新發布的在上面
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch(PDOException $e) {
    echo json_encode([]);
}
?>