<?php
require 'db.php';

try {
    // 檢查 faqs 表有沒有 sort 欄位
    // 注意：這裡是 faqs (有s)
    $columns = $pdo->query("SHOW COLUMNS FROM faqs LIKE 'sort'")->fetch();
    
    if(!$columns) {
        $pdo->exec("ALTER TABLE faqs ADD COLUMN sort INT(11) NOT NULL DEFAULT 0 AFTER id");
        echo "<h1>🎉 FAQ 排序欄位新增成功！</h1>";
    } else {
        echo "<h1>👌 排序欄位已經存在了！</h1>";
    }

    echo "<a href='faq_admin.php'>回 FAQ 管理</a>";

} catch(PDOException $e) {
    echo "錯誤：" . $e->getMessage();
}
?>