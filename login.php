<?php
session_start();
require 'db.php';

// 如果已經登入過，直接轉跳後台
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// 處理登入送出的資料
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // 密碼正確，記錄 session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php"); // 轉跳到後台
        exit;
    } else {
        $error = "帳號或密碼錯誤";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>登入 | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #2F3A34; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 100%; max-width: 400px; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="card p-4 shadow">
        <h3 class="text-center mb-4">BossDesign 後台登入</h3>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="帳號" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="密碼" required>
            </div>
            <button type="submit" class="btn btn-success w-100">登入</button>
        </form>
        <a href="index.php" class="d-block text-center mt-3 text-secondary">回到首頁</a>
    </div>
</body>
</html>