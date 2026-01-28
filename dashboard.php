<?php
session_start();
require 'db.php';

// 1. 安全檢查
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// 2. 抓取客戶留言
try {
    $sql = "SELECT * FROM contacts ORDER BY created_at DESC";
    $contacts = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $error = "讀取資料失敗：" . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理後台 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .sidebar { min-height: 100vh; background: #2F3A34; color: white; }
        .sidebar a { color: #aaa; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #2E8B57; color: white; padding-left: 25px; }
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .table-card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar col-md-2 d-none d-md-block">
        <div class="p-4">
            <h4 class="fw-bold mb-0">BossDesign</h4>
            <small class="text-white-50">管理系統</small>
        </div>
        <hr class="border-secondary mt-0">
        <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> 儀表板首頁</a>
        <a href="home_admin.php"><i class="fas fa-building me-2"></i> 首頁內容管理</a>
        <a href="company_admin.php"><i class="fas fa-building me-2"></i> 公司資訊管理</a>
        <a href="project_admin.php"><i class="fas fa-images me-2"></i> 作品集管理</a>
        <a href="news_admin.php"><i class="fas fa-newspaper me-2"></i> 部落格管理</a>
        <a href="faq_admin.php"><i class="fas fa-question-circle me-2"></i> 常見問題管理</a>
        <hr class="border-secondary">
        <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> 登出</a>
    </div>

    <div class="col-md-10 col-12 p-4">
        
        <div class="d-md-none d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0">BossDesign</h4>
            <a href="logout.php" class="btn btn-sm btn-outline-danger">登出</a>
        </div>

        <h2 class="mb-4 fw-bold text-dark">歡迎回來，管理員</h2>

        <h4 class="mb-3 fw-bold text-secondary"><i class="fas fa-inbox me-2"></i>客戶需求留言</h4>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card table-card">
            <div class="card-body p-0">
                <?php if(count($contacts) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 ps-4">時間</th>
                                    <th class="py-3">姓名</th>
                                    <th class="py-3">聯絡方式</th>
                                    <th class="py-3">需求類型</th>
                                    <th class="py-3">備註訊息</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($contacts as $row): ?>
                                <tr>
                                    <td class="ps-4 text-muted small" style="min-width: 120px;">
                                        <?php echo date('Y/m/d H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </td>
                                    <td>
                                        <div class="small"><i class="far fa-envelope me-1 text-secondary"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                        <?php if(!empty($row['phone'])): ?>
                                            <div class="small mt-1"><i class="fas fa-phone me-1 text-secondary"></i> <?php echo htmlspecialchars($row['phone']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary fw-normal px-3 py-2"><?php echo htmlspecialchars($row['issue']); ?></span>
                                    </td>
                                    <td class="text-secondary" style="max-width: 300px;">
                                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                        <p>目前還沒有任何留言喔！</p>
                        <a href="index.html#contact" target="_blank" class="btn btn-outline-success btn-sm">去前台測試一筆</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>