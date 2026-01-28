<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'basic';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tab = $_POST['tab'];

    // 1. 更新基本資料
    if ($_POST['action'] == 'update_info') {
        $id = 1;
        $about_image = $_POST['old_about_image'];
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] == 0) {
            $ext = pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION);
            $about_image = "about_" . time() . "." . $ext;
            move_uploaded_file($_FILES['about_image']['tmp_name'], "uploads/" . $about_image);
        }
        $founder_image = $_POST['old_founder_image'];
        if (isset($_FILES['founder_image']) && $_FILES['founder_image']['error'] == 0) {
            $ext = pathinfo($_FILES['founder_image']['name'], PATHINFO_EXTENSION);
            $founder_image = "founder_" . time() . "." . $ext;
            move_uploaded_file($_FILES['founder_image']['tmp_name'], "uploads/" . $founder_image);
        }
        $sql = "UPDATE company_info SET about_title=?, about_desc=?, about_image=?, founder_name=?, founder_title=?, founder_quote=?, founder_desc=?, founder_image=? WHERE id=?";
        $pdo->prepare($sql)->execute([$_POST['about_title'], $_POST['about_desc'], $about_image, $_POST['founder_name'], $_POST['founder_title'], $_POST['founder_quote'], $_POST['founder_desc'], $founder_image, $id]);
    }

    // 2. 更新影片 (Reels)
    if ($_POST['action'] == 'add_reel' || $_POST['action'] == 'update_reel') {
        $image = $_POST['old_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = "reel_" . time() . "_" . rand(100,999) . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        }
        if ($_POST['action'] == 'add_reel') {
            $pdo->prepare("INSERT INTO about_reels (title, youtube_url, image, sort_order) VALUES (?, ?, ?, ?)")->execute([$_POST['title'], $_POST['youtube_url'], $image, $_POST['sort_order']]);
        } else {
            $pdo->prepare("UPDATE about_reels SET title=?, youtube_url=?, image=?, sort_order=? WHERE id=?")->execute([$_POST['title'], $_POST['youtube_url'], $image, $_POST['sort_order'], $_POST['id']]);
        }
    }

    // 3. 更新列表項目 (Philosophy, Workflow)
    if ($_POST['action'] == 'update_item') {
        $pdo->prepare("UPDATE {$_POST['table']} SET title=?, desc_text=?, icon=? WHERE id=?")->execute([$_POST['title'], $_POST['desc_text'], $_POST['icon'], $_POST['id']]);
    }

    // 4. 更新系統整合 (System) - 有圖片
    if ($_POST['action'] == 'update_system') {
        $image = $_POST['old_image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = "sys_" . time() . "_" . $_POST['id'] . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        }
        $pdo->prepare("UPDATE about_system SET title=?, desc_text=?, image=? WHERE id=?")->execute([$_POST['title'], $_POST['desc_text'], $image, $_POST['id']]);
    }

    // 5. 更新觀點 (Insights) - 新增
    if ($_POST['action'] == 'update_insight') {
        $image = $_POST['old_image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = "insight_" . time() . "_" . $_POST['id'] . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        }
        $pdo->prepare("UPDATE about_insights SET category=?, title=?, summary=?, content=?, image=? WHERE id=?")->execute([$_POST['category'], $_POST['title'], $_POST['summary'], $_POST['content'], $image, $_POST['id']]);
    }

    echo "<script>alert('✅ 更新成功！'); window.location.href='company_admin.php?tab=$tab';</script>";
}

// 讀取資料
$info = $pdo->query("SELECT * FROM company_info WHERE id=1")->fetch();
$reels = $pdo->query("SELECT * FROM about_reels ORDER BY sort_order ASC")->fetchAll();
$philosophy = $pdo->query("SELECT * FROM about_philosophy ORDER BY sort ASC")->fetchAll();
$workflow = $pdo->query("SELECT * FROM about_workflow ORDER BY sort ASC")->fetchAll();
$system = $pdo->query("SELECT * FROM about_system ORDER BY sort ASC")->fetchAll();
$insights = $pdo->query("SELECT * FROM about_insights ORDER BY sort ASC")->fetchAll();

$edit_reel_mode = false;
$current_reel = ['title'=>'', 'youtube_url'=>'', 'image'=>'', 'sort_order'=>'10', 'id'=>''];
if (isset($_GET['edit_reel'])) {
    $active_tab = 'reels';
    $current_reel = $pdo->query("SELECT * FROM about_reels WHERE id=".$_GET['edit_reel'])->fetch();
    $edit_reel_mode = true;
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>公司資訊整合管理 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .preview-img { height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .preview-img-lg { max-width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-top: 10px; }
        .nav-tabs .nav-link { color: #555; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #2E8B57; font-weight: 700; border-top: 3px solid #2E8B57; }
        .card { border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .editor-container { background: white; border-radius: 8px; padding: 25px; border-top: 4px solid #dc3545; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3 mb-4">
    <a class="navbar-brand" href="dashboard.php">BossDesign 後台</a>
    <div class="ms-auto"><a href="dashboard.php" class="btn btn-outline-light btn-sm">回儀表板</a></div>
</nav>

<div class="container pb-5">
    <h3 class="mb-4 text-secondary"><i class="fas fa-edit me-2"></i>全站內容管理系統</h3>

    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='basic'?'active':''; ?>" href="?tab=basic">基本資訊</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='reels'?'active':''; ?>" href="?tab=reels">影片專區</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='philosophy'?'active':''; ?>" href="?tab=philosophy">設計主旨</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='workflow'?'active':''; ?>" href="?tab=workflow">服務流程</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab=='system'?'active':''; ?>" href="?tab=system">系統整合</a></li>
    </ul>

    <?php if($active_tab == 'basic'): ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_info"><input type="hidden" name="tab" value="basic">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card p-4 h-100">
                    <h5 class="text-success mb-3">公司介紹</h5>
                    <div class="mb-3"><label class="fw-bold">大標題</label><input type="text" name="about_title" class="form-control" value="<?php echo htmlspecialchars($info['about_title']); ?>"></div>
                    <div class="mb-3"><label class="fw-bold">內文</label><textarea name="about_desc" class="form-control" rows="5"><?php echo htmlspecialchars($info['about_desc']); ?></textarea></div>
                    <div class="mb-3"><label class="fw-bold">形象圖</label><input type="file" name="about_image" class="form-control"><input type="hidden" name="old_about_image" value="<?php echo $info['about_image']; ?>"><?php if($info['about_image']) echo '<img src="uploads/'.$info['about_image'].'" class="preview-img-lg">'; ?></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-4 h-100">
                    <h5 class="text-primary mb-3">創辦人</h5>
                    <div class="row"><div class="col-6 mb-3"><label class="fw-bold">姓名</label><input type="text" name="founder_name" class="form-control" value="<?php echo htmlspecialchars($info['founder_name']); ?>"></div><div class="col-6 mb-3"><label class="fw-bold">頭銜</label><input type="text" name="founder_title" class="form-control" value="<?php echo htmlspecialchars($info['founder_title']); ?>"></div></div>
                    <div class="mb-3"><label class="fw-bold">名言</label><input type="text" name="founder_quote" class="form-control" value="<?php echo htmlspecialchars($info['founder_quote']); ?>"></div>
                    <div class="mb-3"><label class="fw-bold">介紹</label><textarea name="founder_desc" class="form-control" rows="4"><?php echo htmlspecialchars($info['founder_desc']); ?></textarea></div>
                    <div class="mb-3"><label class="fw-bold">照片</label><input type="file" name="founder_image" class="form-control"><input type="hidden" name="old_founder_image" value="<?php echo $info['founder_image']; ?>"><?php if($info['founder_image']) echo '<img src="uploads/'.$info['founder_image'].'" class="preview-img-lg">'; ?></div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-dark btn-lg w-100">儲存變更</button>
    </form>
    <?php endif; ?>

    <?php if($active_tab == 'reels'): ?>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="editor-container <?php echo $edit_reel_mode ? 'edit-mode' : ''; ?>">
                <h5 class="border-bottom pb-2 mb-3"><?php echo $edit_reel_mode ? '✏️ 編輯影片' : '🎥 新增影片'; ?></h5>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tab" value="reels"><input type="hidden" name="action" value="<?php echo $edit_reel_mode ? 'update_reel' : 'add_reel'; ?>">
                    <?php if($edit_reel_mode): ?><input type="hidden" name="id" value="<?php echo $current_reel['id']; ?>"><input type="hidden" name="old_image" value="<?php echo $current_reel['image']; ?>"><?php endif; ?>
                    <div class="mb-3"><label class="fw-bold">排序</label><input type="number" name="sort_order" class="form-control" value="<?php echo $current_reel['sort_order']; ?>" required></div>
                    <div class="mb-3"><label class="fw-bold">標題</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($current_reel['title']); ?>" required></div>
                    <div class="mb-3"><label class="fw-bold">YouTube 網址</label><input type="text" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($current_reel['youtube_url']); ?>" required></div>
                    <div class="mb-3"><label class="fw-bold">封面</label><input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_reel_mode ? '' : 'required'; ?>></div>
                    <button type="submit" class="btn btn-danger w-100"><?php echo $edit_reel_mode ? '確認修改' : '發佈影片'; ?></button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-0"><table class="table table-hover mb-0"><tbody><?php foreach($reels as $row): ?><tr><td><?php echo $row['sort_order']; ?></td><td><?php if($row['image']) echo '<img src="uploads/'.$row['image'].'" class="preview-img">'; ?></td><td><?php echo $row['title']; ?></td><td><a href="?tab=reels&edit_reel=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">編輯</a></td></tr><?php endforeach; ?></tbody></table></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($active_tab == 'philosophy'): ?>
    <div class="row"><?php foreach($philosophy as $row): ?><div class="col-md-4 mb-4"><div class="card p-3 h-100"><form method="POST"><input type="hidden" name="action" value="update_item"><input type="hidden" name="table" value="about_philosophy"><input type="hidden" name="tab" value="philosophy"><input type="hidden" name="id" value="<?php echo $row['id']; ?>"><div class="mb-2 text-center text-primary"><i class="<?php echo $row['icon']; ?> fa-2x"></i></div><div class="mb-2"><label>Icon</label><input type="text" name="icon" class="form-control form-control-sm" value="<?php echo $row['icon']; ?>"></div><div class="mb-2"><label>標題</label><input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>"></div><div class="mb-3"><label>內容</label><textarea name="desc_text" class="form-control" rows="4"><?php echo $row['desc_text']; ?></textarea></div><button type="submit" class="btn btn-sm btn-outline-primary w-100">更新</button></form></div></div><?php endforeach; ?></div>
    <?php endif; ?>

    <?php if($active_tab == 'workflow'): ?>
    <div class="row"><?php foreach($workflow as $row): ?><div class="col-md-3 mb-4"><div class="card p-3 h-100"><form method="POST"><input type="hidden" name="action" value="update_item"><input type="hidden" name="table" value="about_workflow"><input type="hidden" name="tab" value="workflow"><input type="hidden" name="id" value="<?php echo $row['id']; ?>"><div class="mb-2 text-center text-success"><i class="<?php echo $row['icon']; ?> fa-2x"></i></div><div class="mb-2"><label>Icon</label><input type="text" name="icon" class="form-control form-control-sm" value="<?php echo $row['icon']; ?>"></div><div class="mb-2"><label>標題</label><input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>"></div><div class="mb-3"><label>內容</label><textarea name="desc_text" class="form-control" rows="4"><?php echo $row['desc_text']; ?></textarea></div><button type="submit" class="btn btn-sm btn-outline-success w-100">更新</button></form></div></div><?php endforeach; ?></div>
    <?php endif; ?>

    <?php if($active_tab == 'system'): ?>
    <div class="row"><?php foreach($system as $row): ?><div class="col-md-3 mb-4"><div class="card p-3 h-100"><form method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="update_system"><input type="hidden" name="tab" value="system"><input type="hidden" name="id" value="<?php echo $row['id']; ?>"><div class="mb-2"><?php if($row['image']) echo '<img src="uploads/'.$row['image'].'" class="preview-img w-100 mb-2">'; ?><input type="file" name="image" class="form-control form-control-sm"><input type="hidden" name="old_image" value="<?php echo $row['image']; ?>"></div><div class="mb-2"><label>標題</label><input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>"></div><div class="mb-3"><label>描述</label><textarea name="desc_text" class="form-control" rows="3"><?php echo $row['desc_text']; ?></textarea></div><button type="submit" class="btn btn-sm btn-outline-dark w-100">更新</button></form></div></div><?php endforeach; ?></div>
    <?php endif; ?>

</div>
</body>
</html>