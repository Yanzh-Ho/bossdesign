<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// --- 處理表單提交 (新增 或 更新) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $url = $_POST['youtube_url'];
    $sort_order = (int)$_POST['sort_order'];
    
    // 處理圖片
    $image = $_POST['old_image'] ?? ''; // 預設使用舊圖
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    if ($_POST['action'] == 'add') {
        // 新增
        $sql = "INSERT INTO about_reels (title, youtube_url, image, sort_order) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $url, $image, $sort_order]);
        echo "<script>alert('✨ 新增成功！'); window.location.href='about_admin.php';</script>";
        
    } elseif ($_POST['action'] == 'update') {
        // 更新
        $id = $_POST['id'];
        $sql = "UPDATE about_reels SET title=?, youtube_url=?, image=?, sort_order=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $url, $image, $sort_order, $id]);
        echo "<script>alert('✅ 修改完成！'); window.location.href='about_admin.php';</script>";
    }
}

// --- 刪除 ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM about_reels WHERE id = ?")->execute([$id]);
    header("Location: about_admin.php"); exit;
}

// --- 編輯模式：抓取資料 ---
$edit_mode = false;
$current = ['title'=>'', 'youtube_url'=>'', 'image'=>'', 'sort_order'=>'10', 'id'=>''];

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM about_reels WHERE id = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch();
    if ($data) {
        $edit_mode = true;
        $current = $data;
    }
}

// 撈出所有資料 (依照 排序數字 小到大，再依照 時間 新到舊)
$reels = $pdo->query("SELECT * FROM about_reels ORDER BY sort_order ASC, created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>關於我們管理 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .editor-container { background: white; border-radius: 8px; padding: 30px; border-top: 4px solid #dc3545; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .edit-mode { border-top-color: #ffc107; }
        .preview-img { width: 60px; height: 90px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .edit-preview-img { max-width: 100%; height: 150px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; margin-top: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3 mb-4">
    <a class="navbar-brand" href="dashboard.php">BossDesign 後台</a>
    <div class="ms-auto">
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">回儀表板</a>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4 text-secondary"><i class="fab fa-youtube me-2"></i>影音專區管理 </h3>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="editor-container <?php echo $edit_mode ? 'edit-mode' : ''; ?>">
                <h5 class="border-bottom pb-2 mb-3">
                    <?php echo $edit_mode ? '編輯影片' : '新增影片'; ?>
                </h5>
                
                <form method="POST" enctype="multipart/form-data" action="about_admin.php">
                    <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="id" value="<?php echo $current['id']; ?>">
                        <input type="hidden" name="old_image" value="<?php echo $current['image']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">排序</label>
                        <input type="number" name="sort_order" class="form-control" value="<?php echo $current['sort_order']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">標題 (Title)</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($current['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">YouTube 網址</label>
                        <input type="text" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($current['youtube_url']); ?>" placeholder="https://youtube.com/shorts/..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">封面圖片</label>
                        <input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_mode ? '' : 'required'; ?>>
                        
                        <?php if($edit_mode && $current['image']): ?>
                            <div class="mt-2 small text-muted">目前封面：</div>
                            <img src="uploads/<?php echo $current['image']; ?>" class="edit-preview-img">
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?php if($edit_mode): ?>
                            <button type="submit" class="btn btn-warning fw-bold text-dark">確認修改</button>
                            <a href="about_admin.php" class="btn btn-outline-secondary">取消</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-danger fw-bold">發佈影片</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 text-secondary">影片列表 (依序排列)</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="60">序</th>
                                <th width="80">封面</th>
                                <th>標題 / 連結</th>
                                <th class="text-end pe-4" width="140">管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reels as $row): ?>
                            <tr class="<?php echo ($edit_mode && $row['id'] == $edit_id) ? 'table-warning' : ''; ?>">
                                <td class="text-center fw-bold text-secondary"><?php echo $row['sort_order']; ?></td>
                                <td>
                                    <?php if($row['image']): ?>
                                        <img src="uploads/<?php echo $row['image']; ?>" class="preview-img">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">無圖</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($row['title']); ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 250px;">
                                        <a href="<?php echo $row['youtube_url']; ?>" target="_blank" class="text-decoration-none text-danger">
                                            <i class="fab fa-youtube"></i> 開啟影片
                                        </a>
                                    </small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="編輯">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('確定刪除？');" title="刪除">
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

</body>
</html>