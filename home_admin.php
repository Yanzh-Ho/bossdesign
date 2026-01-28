<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'carousel';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tab = $_POST['tab'];

    // 更新輪播圖
    if ($_POST['action'] == 'update_carousel') {
        $image = $_POST['old_image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = "hero_" . time() . "_" . $_POST['id'] . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        }
        $pdo->prepare("UPDATE home_carousel SET title=?, subtitle=?, image=? WHERE id=?")->execute([$_POST['title'], $_POST['subtitle'], $image, $_POST['id']]);
    }

    // 更新數據
    if ($_POST['action'] == 'update_stat') {
        $pdo->prepare("UPDATE home_stats SET number=?, unit=?, prefix=?, label=? WHERE id=?")->execute([$_POST['number'], $_POST['unit'], $_POST['prefix'], $_POST['label'], $_POST['id']]);
    }

    echo "<script>alert('✅ 更新成功！'); window.location.href='home_admin.php?tab=$tab';</script>";
}

$carousels = $pdo->query("SELECT * FROM home_carousel ORDER BY sort ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM home_stats ORDER BY sort ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>首頁管理 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .preview-img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
        .nav-tabs .nav-link.active { color: #2E8B57; border-top: 3px solid #2E8B57; font-weight: bold; }
        .nav-tabs .nav-link { color: #555; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3 mb-4">
    <a class="navbar-brand" href="dashboard.php">BossDesign 後台</a>
    <div class="ms-auto"><a href="dashboard.php" class="btn btn-outline-light btn-sm">回儀表板</a></div>
</nav>

<div class="container pb-5">
    <h3 class="mb-4 text-secondary"><i class="fas fa-home me-2"></i>首頁內容管理</h3>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='carousel'?'active':''; ?>" href="?tab=carousel">封面輪播</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='stats'?'active':''; ?>" href="?tab=stats">數據統計</a></li>
    </ul>

    <?php if($active_tab == 'carousel'): ?>
    <div class="row">
        <?php foreach($carousels as $row): 
            $img = strpos($row['image'], '首頁/') === 0 ? $row['image'] : "uploads/".$row['image'];
        ?>
        <div class="col-md-4 mb-4">
            <div class="card p-3 h-100 shadow-sm border-0">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_carousel">
                    <input type="hidden" name="tab" value="carousel">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    
                    <img src="<?php echo $img; ?>" class="preview-img">
                    <input type="file" name="image" class="form-control form-control-sm mb-3">
                    <input type="hidden" name="old_image" value="<?php echo $row['image']; ?>">
                    
                    <div class="mb-2"><label class="fw-bold">大標題</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($row['title']); ?>"></div>
                    <div class="mb-3"><label class="fw-bold">副標題</label><textarea name="subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($row['subtitle']); ?></textarea></div>
                    
                    <button type="submit" class="btn btn-dark w-100">更新</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if($active_tab == 'stats'): ?>
    <div class="row">
        <?php foreach($stats as $row): ?>
        <div class="col-md-3 mb-4">
            <div class="card p-3 h-100 shadow-sm border-0 text-center">
                <form method="POST">
                    <input type="hidden" name="action" value="update_stat">
                    <input type="hidden" name="tab" value="stats">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    
                    <h1 class="text-warning fw-bold mb-3"><?php echo $row['number']; ?><span class="fs-6 text-muted"><?php echo $row['unit']; ?></span></h1>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-8"><label class="small text-muted">數字</label><input type="number" name="number" class="form-control text-center fw-bold" value="<?php echo $row['number']; ?>"></div>
                        <div class="col-4"><label class="small text-muted">單位</label><input type="text" name="unit" class="form-control text-center" value="<?php echo $row['unit']; ?>"></div>
                    </div>
                    <div class="mb-2"><label class="small text-muted">前綴 (如 STC)</label><input type="text" name="prefix" class="form-control text-center" value="<?php echo $row['prefix']; ?>"></div>
                    <div class="mb-3"><label class="small text-muted">說明標籤</label><input type="text" name="label" class="form-control text-center" value="<?php echo $row['label']; ?>"></div>
                    
                    <button type="submit" class="btn btn-outline-warning w-100 text-dark">更新</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>