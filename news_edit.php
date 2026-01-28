<?php
session_start();
require 'db.php';

// 安全檢查
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// 1. 取得要編輯的文章 ID
if (!isset($_GET['id'])) { header("Location: news_admin.php"); exit; }
$id = $_GET['id'];

// 2. 處理更新請求 (按下儲存時)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    $link = $_POST['link'];

    // 處理圖片 (如果有上傳新圖才更新，否則維持原圖)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $filename = time() . "_n_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // 更新包含圖片的 SQL
            $sql = "UPDATE news SET title=?, content=?, date=?, category=?, link=?, image=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $date, $category, $link, $filename, $id]);
        }
    } else {
        // 只更新文字，不改圖片
        $sql = "UPDATE news SET title=?, content=?, date=?, category=?, link=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $date, $category, $link, $id]);
    }

    echo "<script>alert('✨ 修改成功！'); window.location.href='news_admin.php';</script>";
}

// 3. 撈出這篇文章的舊資料 (為了填入表單)
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { echo "找不到文章"; exit; }

// 撈出用過的分類 (給選單用)
$stmt_cat = $pdo->query("SELECT DISTINCT category FROM news");
$used_categories = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>編輯文章 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .editor-container { background: white; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); padding: 30px; }
        .form-label { font-weight: bold; color: #333; margin-top: 10px; }
        .img-preview { max-width: 100%; height: auto; border-radius: 8px; margin-top: 10px; border: 2px dashed #ddd; padding: 5px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="dashboard.php">BossDesign 管理後台</a>
</nav>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-secondary"><i class="fas fa-pencil-alt me-2"></i>編輯文章</h3>
                <a href="news_admin.php" class="btn btn-outline-secondary">取消返回</a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="editor-container h-100">
                            <h5 class="border-bottom pb-2 mb-3">⚙️ 設定</h5>
                            
                            <label class="form-label">日期</label>
                            <input type="date" name="date" class="form-control" value="<?php echo $post['date']; ?>" required>

                            <label class="form-label">分類標籤</label>
                            <input type="text" name="category" list="category_list" class="form-control" value="<?php echo htmlspecialchars($post['category']); ?>" required>
                            <datalist id="category_list">
                                <option value="最新公告">
                                <option value="產業觀點">
                                <option value="媒體報導">
                                <?php foreach($used_categories as $cat): ?>
                                    <?php if(!in_array($cat, ['最新公告', '產業觀點', '媒體報導'])) echo "<option value='$cat'>"; ?>
                                <?php endforeach; ?>
                            </datalist>

                            <label class="form-label">封面圖片</label>
                            <?php if($post['image']): ?>
                                <div class="mb-2">
                                    <span class="badge bg-secondary mb-1">目前圖片</span>
                                    <img src="uploads/<?php echo $post['image']; ?>" class="img-preview d-block">
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" name="image" class="form-control mt-2" accept="image/*" onchange="previewImage(this)">
                            <div class="form-text">若不換圖請留空</div>
                            <img id="preview" class="img-preview" style="display:none;" alt="新圖片預覽">

                            <label class="form-label">外部連結</label>
                            <input type="text" name="link" class="form-control" value="<?php echo htmlspecialchars($post['link']); ?>">
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="editor-container h-100">
                            <label class="form-label">文章標題</label>
                            <input type="text" name="title" class="form-control form-control-lg mb-3 fw-bold" value="<?php echo htmlspecialchars($post['title']); ?>" required>

                            <label class="form-label">內文</label>
                            <textarea id="summernote" name="content"><?php echo $post['content']; ?></textarea>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-5 fw-bold">儲存修改</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#summernote').summernote({
        placeholder: '文章內容...',
        tabsize: 2,
        height: 400,
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