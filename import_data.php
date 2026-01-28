<?php
require 'db.php';

// 這是從您的 HTML 裡提取出來的資料
$projects = [
    [
        'title' => '手機殼大廠總部',
        'category' => '辦公室設計', // 資料庫必填，我們先預設為此分類
        'old_path' => 'r/犀牛頓7.jpg' // 原本第一張照片的路徑
    ],
    [
        'title' => 'TSMC協力辦公室',
        'category' => '辦公室設計',
        'old_path' => '帆宣/帆宣cover.jpg'
    ],
    [
        'title' => 'Hermeneutic',
        'category' => '辦公室設計',
        'old_path' => '富邦/hermeneutic.jpg'
    ],
    [
        'title' => '世祥汽材營運總部',
        'category' => '辦公室設計',
        'old_path' => 'c/c封面.jpg'
    ],
    [
        'title' => 'Kronos Research',
        'category' => '辦公室設計',
        'old_path' => 'ks/kronos research.jpg'
    ],
    [
        'title' => 'Kronos & Woo',
        'category' => '辦公室設計',
        'old_path' => 'kw/kronos&woo.jpg'
    ],
    [
        'title' => 'Beckhoff Automation',
        'category' => '辦公室設計',
        'old_path' => 'b/beckhoff.jpg'
    ],
    [
        'title' => 'BossDesign 辦公室',
        'category' => '實驗基地', // 這筆比較特別，我幫您改個分類
        'old_path' => 'bs/office maker.jpg'
    ],
    [
        'title' => 'Specialized Bicycle',
        'category' => '辦公室設計',
        'old_path' => 's/s.cover.JPG'
    ],
    [
        'title' => 'Idea House 辦公室',
        'category' => '辦公室設計',
        'old_path' => 'i/i封面.jpg'
    ],
    [
        'title' => '臺中國家歌劇院',
        'category' => '公共空間', // 這筆分類我幫您微調
        'old_path' => 'o/o封面.JPG'
    ]
];

echo "<h1>🚀 開始自動搬家...</h1>";
echo "<hr>";

// 確保 uploads 資料夾存在
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

foreach ($projects as $p) {
    $title = $p['title'];
    $category = $p['category'];
    $source_file = $p['old_path']; // 舊路徑 (例如: r/犀牛頓7.jpg)

    // 檢查舊照片是否真的存在
    if (file_exists($source_file)) {
        
        // 為了避免中文檔名亂碼，我們幫新檔案取一個獨立的名字 (時間戳 + 原檔名)
        $extension = pathinfo($source_file, PATHINFO_EXTENSION);
        $new_filename = time() . "_" . rand(100,999) . "." . $extension;
        $target_file = "uploads/" . $new_filename;

        // 1. 複製檔案
        if (copy($source_file, $target_file)) {
            
            // 2. 寫入資料庫
            // 先檢查是否已經有這筆資料 (避免重複執行時一直新增)
            $check = $pdo->prepare("SELECT id FROM projects WHERE title = ?");
            $check->execute([$title]);
            
            if ($check->rowCount() == 0) {
                $sql = "INSERT INTO projects (title, category, image) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$title, $category, $new_filename]);
                echo "<p style='color:green'>✅ 成功匯入：{$title}</p>";
            } else {
                echo "<p style='color:gray'>⚠️ 跳過 (已存在)：{$title}</p>";
            }

        } else {
            echo "<p style='color:red'>❌ 複製失敗：{$title} (無法將照片複製到 uploads)</p>";
        }

    } else {
        echo "<p style='color:red'>❌ 找不到原始照片：{$source_file} (請確認您的資料夾名稱是否正確)</p>";
    }
}

echo "<hr>";
echo "<h3>🎉 搬家完成！</h3>";
echo "<a href='project_admin.php' style='font-size:20px'>👉 點我回後台檢查</a>";
?>