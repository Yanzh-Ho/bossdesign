<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // 這一行就是去倉庫拿貨：撈出所有新聞
    $stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC");
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 把資料打包成 JSON 格式送出去
    echo json_encode($news);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>