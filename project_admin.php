<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// --- 新增作品 ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $title = $_POST['title'];
        $category = $_POST['category'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $filename = time() . "_" . basename($_FILES["image"]["name"]); 
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $filename)) {
                $pdo->prepare("INSERT INTO projects (title, category, image) VALUES (?, ?, ?)")->execute([$title, $category, $filename]); 
                echo "<script>alert('✨ 作品新增成功！'); window.location.href='project_admin.php';</script>";
            }
        }
    }
    // --- 更新排序 ---
    elseif ($_POST['action'] == 'update_sort') {
        foreach ($_POST['sort'] as $id => $val) {
            $pdo->prepare("UPDATE projects SET sort = ? WHERE id = ?")->execute([(int)$val, $id]);
        }
        echo "<script>alert('✅ 排序更新成功！'); window.location.href='project_admin.php';</script>";
    }
}

// --- 刪除作品 ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $img = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
    $img->execute([$id]);
    $row = $img->fetch();
    if ($row) {
        $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        if(file_exists("uploads/" . $row['image'])) unlink("uploads/" . $row['image']);
        header("Location: project_admin.php"); exit;
    }
}

// 依照 sort (小到大) 排序，如果 sort 一樣則依照建立時間
$projects = $pdo->query("SELECT * FROM projects ORDER BY sort ASC, created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>作品集管理 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .project-table img { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; }
        .sort-input { width: 60px; text-align: center; font-weight: bold; border: 1px solid #ddd; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3 mb-4">
    <a class="navbar-brand" href="dashboard.php">BossDesign 管理後台</a>
    <a href="dashboard.php" class="btn btn-outline-light btn-sm ms-auto">回首頁</a>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="mb-3">快速新增</h5>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="title" class="form-control mb-2" placeholder="專案名稱" required>
                    <select name="category" class="form-select mb-2">
                        <option value="辦公室設計">辦公室設計</option>
                        <option value="ESG 改造">ESG 改造</option>
                        <option value="公共空間">公共空間</option>
                    </select>
                    <input type="file" name="image" class="form-control mb-3" accept="image/*" required>
                    <button type="submit" class="btn btn-success w-100">發佈</button>
                </form>
                <small class="text-muted mt-2 d-block text-center">詳細資料可於發佈後點擊編輯補充</small>
            </div>
        </div>

        <div class="col-md-8">
            <form method="POST">
                <input type="hidden" name="action" value="update_sort">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="m-0 text-secondary"><i class="fas fa-list me-2"></i>作品列表</h5>
                        <button type="submit" class="btn btn-sm btn-dark"><i class="fas fa-save me-1"></i> 儲存排序</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0 project-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" width="10%">排序</th>
                                    <th>圖片</th>
                                    <th>名稱 / 資訊</th>
                                    <th class="text-end pe-4">管理</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($projects as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <input type="number" name="sort[<?php echo $p['id']; ?>]" value="<?php echo $p['sort']; ?>" class="form-control form-control-sm sort-input">
                                    </td>
                                    <td><img src="uploads/<?php echo $p['image']; ?>"></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></div>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($p['category']); ?></span>
                                        <?php if(!empty($p['location'])): ?>
                                            <small class="text-muted ms-2"><i class="fas fa-map-marker-alt"></i> <?php echo $p['location']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="projects.php" target="_blank" class="btn btn-sm btn-outline-info me-1" title="查看前台"><i class="fas fa-eye"></i></a>
                                        <a href="project_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="編輯詳情"><i class="fas fa-edit"></i></a>
                                        <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('確定刪除？');"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>