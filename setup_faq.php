<?php
require 'db.php';

try {
    // 建立 FAQ 表格
    $pdo->exec("CREATE TABLE IF NOT EXISTS `faq` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `question` varchar(255) NOT NULL,
        `answer` text NOT NULL,
        `sort` int(11) NOT NULL DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 預設資料
    if($pdo->query("SELECT count(*) FROM faq")->fetchColumn() == 0){
        $pdo->exec("INSERT INTO faq (question, answer, sort) VALUES 
        ('設計專案的流程通常需要多久？', '一般而言，從初步洽談、設計提案到施工完成，約需 2-4 個月。具體時間取決於坪數大小與設計複雜度。', 1),
        ('你們有提供免費丈量服務嗎？', '有的，我們提供大台北地區免費初步丈量與諮詢服務。外縣市則會酌收車馬費，若簽約則可折抵。', 2),
        ('預算該如何抓？', '我們會建議依據您的需求（裝修、家具、設備）進行分項估算。一般商辦裝修建議預算範圍為每坪 3-8 萬元不等。', 3)");
    }

    echo "<h1>🎉 FAQ 資料庫建立成功！</h1>";
    echo "<a href='faq.php'>馬上去前台看看</a>";

} catch(PDOException $e) {
    echo "錯誤：" . $e->getMessage();
}
?>