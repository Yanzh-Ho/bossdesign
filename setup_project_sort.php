<?php
require 'db.php';

try {
    // 檢查 projects 表有沒有 sort 欄位，沒有就加上去
    $columns = $pdo->query("SHOW COLUMNS FROM projects LIKE 'sort'")->fetch();
    
    if(!$columns) {
        $pdo->exec("ALTER TABLE projects ADD COLUMN sort INT(11) NOT NULL DEFAULT 0 AFTER id");
        echo "<h1>🎉 排序欄位新增成功！</h1>";
    } else {
        echo "<h1>👌 排序欄位已經存在了，不用擔心！</h1>";
    }

    echo "<a href='project_admin.php'>回作品管理</a>";

} catch(PDOException $e) {
    echo "錯誤：" . $e->getMessage();
}
?>