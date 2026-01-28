<?php
session_start();
require 'db.php';

// 安全檢查
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// --- 處理新增新聞 ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $title = $_POST['title'];
    $content = $_POST['content']; 
    $date = $_POST['date'];
    $category = $_POST['category'];
    $link = $_POST['link'];
    $image = '';

    // 處理圖片
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $filename = time() . "_n_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $filename;
        }
    }

    $sql = "INSERT INTO news (title, content, date, category, image, link) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $content, $date, $category, $image, $link]);
    echo "<script>alert('✨ 文章發佈成功！'); window.location.href='news_admin.php';</script>";
}

// --- 處理刪除 ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
        if($row['image'] && file_exists("uploads/" . $row['image'])){
            unlink("uploads/" . $row['image']);
        }
        header("Location: news_admin.php"); exit;
    }
}

// 撈出所有新聞
$news_list = $pdo->query("SELECT * FROM news ORDER BY date DESC")->fetchAll();
$stmt_cat = $pdo->query("SELECT DISTINCT category FROM news");
$used_categories = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>專業部落格管理 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .editor-container { background: white; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); padding: 30px; }
        .form-label { font-weight: bold; color: #333; margin-top: 10px; }
        .img-preview { max-width: 100%; height: auto; border-radius: 8px; margin-top: 10px; display: none; border: 2px dashed #ddd; padding: 5px; }
        .news-table img { width: 60px; height: 40px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="dashboard.php">BossDesign 管理後台</a>
    <div class="ms-auto">
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">回首頁</a>
    </div>
</nav>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4 text-secondary"><i class="fas fa-edit me-2"></i>發佈新文章</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="editor-container h-100">
                            <h5 class="border-bottom pb-2 mb-3">基本設定</h5>
                            
                            <label class="form-label">日期</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>

                            <label class="form-label">分類標籤</label>
                            <input type="text" name="category" list="category_list" class="form-control" placeholder="輸入或選擇..." required>
                            <datalist id="category_list">
                                <option value="最新公告">
                                <option value="產業觀點">
                                <option value="媒體報導">
                                <?php foreach($used_categories as $cat): ?>
                                    <?php if(!in_array($cat, ['最新公告', '產業觀點', '媒體報導'])) echo "<option value='$cat'>"; ?>
                                <?php endforeach; ?>
                            </datalist>

                            <label class="form-label">封面圖片</label>
                            <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                            <img id="preview" class="img-preview" alt="圖片預覽">

                            <label class="form-label">外部連結 (選填)</label>
                            <input type="text" name="link" class="form-control" placeholder="例如 Medium 連結">
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="editor-container h-100">
                            <h5 class="border-bottom pb-2 mb-3">內容編輯</h5>
                            <label class="form-label">文章標題</label>
                            <input type="text" name="title" class="form-control form-control-lg mb-3" placeholder="請輸入標題" required style="font-weight:bold;">
                            <label class="form-label">內文</label>
                            <textarea id="summernote" name="content"></textarea>
                            <div class="mt-4 text-end">
                                <button type="reset" class="btn btn-secondary me-2">清除重寫</button>
                                <button type="submit" class="btn btn-primary px-5 fw-bold">發佈文章</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 text-secondary">已發佈文章列表</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0 news-table">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">日期</th>
                                <th>封面</th>
                                <th>標題 / 摘要</th>
                                <th>分類</th>
                                <th class="text-end pe-4" style="min-width: 150px;">管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($news_list as $item): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $item['date']; ?></td>
                                <td>
                                    <?php if($item['image']): ?>
                                        <img src="uploads/<?php echo $item['image']; ?>">
                                    <?php else: ?>
                                        <span class="text-muted small">無圖</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <small class="text-muted">
                                        <?php 
                                            echo mb_substr(strip_tags($item['content'] ?? ''), 0, 20) . '...'; 
                                        ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-secondary rounded-pill"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                <td class="text-end pe-4">
                                    <a href="article.html?id=<?php echo $item['id']; ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="前台查看">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="news_edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="編輯修改">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('確定刪除？');" title="刪除">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#summernote').summernote({
        placeholder: '請在此開始撰寫您的文章內容...',
        tabsize: 2,
        height: 350,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview']]
        ]
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>