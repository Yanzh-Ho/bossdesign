<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: project_admin.php"); exit; }

// --- 處理刪除單張相簿照片 ---
if (isset($_GET['del_img'])) {
    $imgId = $_GET['del_img'];
    // 先查檔名
    $stmt = $pdo->prepare("SELECT image FROM project_images WHERE id=? AND project_id=?");
    $stmt->execute([$imgId, $id]);
    $imgRow = $stmt->fetch();
    if ($imgRow) {
        // 刪檔案
        if(file_exists("uploads/" . $imgRow['image'])) unlink("uploads/" . $imgRow['image']);
        // 刪資料庫
        $pdo->prepare("DELETE FROM project_images WHERE id=?")->execute([$imgId]);
    }
    header("Location: project_edit.php?id=" . $id); // 重新整理
    exit;
}

// --- 處理表單儲存 ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $area = $_POST['area'];
    $description = $_POST['description'];

    // 1. 更新基本資料
    $sql = "UPDATE projects SET title=?, category=?, location=?, area=?, description=? WHERE id=?";
    $pdo->prepare($sql)->execute([$title, $category, $location, $area, $description, $id]);

    // 2. 如果有換封面圖
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $filename = time() . "_cover_" . basename($_FILES["cover_image"]["name"]);
        move_uploaded_file($_FILES["cover_image"]["tmp_name"], "uploads/" . $filename);
        $pdo->prepare("UPDATE projects SET image=? WHERE id=?")->execute([$filename, $id]);
    }

    // 3. 處理「更多照片」上傳 (支援多選)
    if (isset($_FILES['gallery'])) {
        $total = count($_FILES['gallery']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['gallery']['error'][$i] == 0) {
                $g_filename = time() . "_{$i}_" . basename($_FILES['gallery']['name'][$i]);
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], "uploads/" . $g_filename)) {
                    // 寫入相簿資料表
                    $pdo->prepare("INSERT INTO project_images (project_id, image) VALUES (?, ?)")
                        ->execute([$id, $g_filename]);
                }
            }
        }
    }

    echo "<script>alert('✨ 修改並上傳成功！'); window.location.href='project_edit.php?id=$id';</script>";
}

// 撈出作品資料
$p = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$p->execute([$id]);
$project = $p->fetch();

// 撈出這個作品的相簿照片
$gallery = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ?");
$gallery->execute([$id]);
$images = $gallery->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>編輯作品 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gallery-item { position: relative; display: inline-block; margin: 5px; }
        .gallery-item img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .btn-del-img { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; text-align: center; line-height: 20px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-4">
                        <h4>編輯作品</h4>
                        <a href="project_admin.php" class="btn btn-secondary">回列表</a>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">專案名稱</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">分類</label>
                                <select name="category" class="form-select">
                                    <option value="辦公室設計" <?php if($project['category']=='辦公室設計') echo 'selected'; ?>>辦公室設計</option>
                                    <option value="ESG 改造" <?php if($project['category']=='ESG 改造') echo 'selected'; ?>>ESG 改造</option>
                                    <option value="公共空間" <?php if($project['category']=='公共空間') echo 'selected'; ?>>公共空間</option>
                                    <option value="實驗基地" <?php if($project['category']=='實驗基地') echo 'selected'; ?>>實驗基地</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">地點</label>
                                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($project['location'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">坪數</label>
                                <input type="text" name="area" class="form-control" value="<?php echo htmlspecialchars($project['area'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">詳細介紹</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">1. 封面圖片 (列表顯示用)</label>
                            <div class="d-flex align-items-center">
                                <img src="uploads/<?php echo $project['image']; ?>" style="width: 120px; height: 90px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                                <input type="file" name="cover_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-success">2. 輪播相簿 (點開後顯示的多張照片)</label>
                            <div class="card bg-light p-3 mb-2">
                                <p class="small text-muted mb-2">已上傳的照片：(點擊紅色 X 可刪除)</p>
                                <div>
                                    <?php foreach($images as $img): ?>
                                    <div class="gallery-item">
                                        <img src="uploads/<?php echo $img['image']; ?>">
                                        <a href="?id=<?php echo $id; ?>&del_img=<?php echo $img['id']; ?>" class="btn-del-img" onclick="return confirm('確定刪除這張照片？')">X</a>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if(count($images) == 0) echo "<span class='text-muted'>目前沒有其他照片</span>"; ?>
                                </div>
                            </div>
                            
                            <label class="form-label">上傳更多照片 (可一次選多張)</label>
                            <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold py-2">儲存所有變更</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>