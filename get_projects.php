<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. 先撈出所有作品
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. 針對每一個作品，去撈它的相簿
    foreach ($projects as &$p) {
        $stmt_img = $pdo->prepare("SELECT image FROM project_images WHERE project_id = ?");
        $stmt_img->execute([$p['id']]);
        // 把查到的照片全部塞進一個 'gallery' 欄位
        $p['gallery'] = $stmt_img->fetchAll(PDO::FETCH_COLUMN);
        
        // 如果相簿是空的，至少要把封面圖放進去，確保輪播有東西看
        if (empty($p['gallery'])) {
            $p['gallery'] = [$p['image']];
        } else {
            // 如果有相簿，通常我們也會想把封面圖放在第一張
            array_unshift($p['gallery'], $p['image']);
            // 去除重複 (如果有人不小心上傳了一樣的)
            $p['gallery'] = array_unique($p['gallery']);
            $p['gallery'] = array_values($p['gallery']); // 重整陣列索引
        }
    }

    echo json_encode($projects);

} catch(PDOException $e) {
    echo json_encode([]);
}
?>