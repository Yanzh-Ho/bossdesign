<?php
session_start();
require 'db.php';

// 1. 安全檢查
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// --- 處理表單提交 ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // A. 快速更新排序 (列表頁按儲存時)
    if (isset($_POST['action']) && $_POST['action'] == 'update_sort') {
        foreach ($_POST['sort'] as $id => $val) {
            $pdo->prepare("UPDATE faqs SET sort = ? WHERE id = ?")->execute([(int)$val, $id]);
        }
        echo "<script>alert('✅ 排序更新成功！'); window.location.href='faq_admin.php';</script>";
        exit;
    }

    // B. 新增或修改單筆資料
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $sort = (int)$_POST['sort']; // 新增排序欄位
    
    if ($_POST['action'] == 'add') {
        $sql = "INSERT INTO faqs (question, answer, sort) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$question, $answer, $sort]);
        echo "<script>alert('✨ 新增成功！'); window.location.href='faq_admin.php';</script>";
        
    } elseif ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $sql = "UPDATE faqs SET question = ?, answer = ?, sort = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$question, $answer, $sort, $id]);
        echo "<script>alert('✅ 修改完成！'); window.location.href='faq_admin.php';</script>";
    }
}

// --- 刪除功能 ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM faqs WHERE id = ?")->execute([$id]);
    header("Location: faq_admin.php"); exit;
}

// --- 編輯模式 ---
$edit_mode = false;
$current_faq = ['question' => '', 'answer' => '', 'id' => '', 'sort' => 10]; // 預設排序10

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch();
    if ($data) {
        $edit_mode = true;
        $current_faq = $data;
    }
}

// 撈出所有資料 (依照 sort 小到大排序)
$faqs = $pdo->query("SELECT * FROM faqs ORDER BY sort ASC, created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>常見問題管理 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: "Noto Sans TC", sans-serif; }
        .editor-container { background: white; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); padding: 30px; border-top: 4px solid #2E8B57; }
        .edit-mode { border-top-color: #FFC107; }
        .sort-input { width: 60px; text-align: center; font-weight: bold; border: 1px solid #ddd; }
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
    <h3 class="mb-4 text-secondary"><i class="fas fa-question-circle me-2"></i>常見問題管理</h3>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="editor-container <?php echo $edit_mode ? 'edit-mode' : ''; ?>">
                <h5 class="border-bottom pb-2 mb-3">
                    <?php if($edit_mode): ?>
                        編輯問題
                    <?php else: ?>
                        新增問題
                    <?php endif; ?>
                </h5>
                
                <form method="POST" action="faq_admin.php">
                    <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="id" value="<?php echo $current_faq['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label fw-bold">排序</label>
                            <input type="number" name="sort" class="form-control" value="<?php echo $current_faq['sort']; ?>">
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold">問題 (Q)</label>
                            <input type="text" name="question" class="form-control" 
                                   value="<?php echo htmlspecialchars($current_faq['question']); ?>" 
                                   placeholder="標題..." required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">回答 (A)</label>
                        <textarea name="answer" class="form-control" rows="8" 
                                  placeholder="請輸入詳細解答..." required><?php echo htmlspecialchars($current_faq['answer']); ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?php if($edit_mode): ?>
                            <button type="submit" class="btn btn-warning fw-bold text-dark">確認修改</button>
                            <a href="faq_admin.php" class="btn btn-outline-secondary">取消編輯</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success fw-bold">發佈問題</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <form method="POST" action="faq_admin.php">
                <input type="hidden" name="action" value="update_sort">
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-secondary">問題列表</h5>
                        <button type="submit" class="btn btn-sm btn-dark"><i class="fas fa-save me-1"></i> 儲存排序</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" width="80">排序</th>
                                    <th>問題內容</th>
                                    <th class="text-end pe-4" width="120">管理</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($faqs as $row): ?>
                                <tr class="<?php echo ($edit_mode && $row['id'] == $edit_id) ? 'table-warning' : ''; ?>">
                                    <td class="ps-4">
                                        <input type="number" name="sort[<?php echo $row['id']; ?>]" value="<?php echo $row['sort']; ?>" class="form-control form-control-sm sort-input">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1">Q: <?php echo htmlspecialchars($row['question']); ?></div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 350px;">
                                            A: <?php echo htmlspecialchars(mb_substr($row['answer'], 0, 50)) . '...'; ?>
                                        </small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-pen"></i></a>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('確定刪除？');"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if(count($faqs) == 0): ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4">目前沒有資料</td></tr>
                                <?php endif; ?>
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