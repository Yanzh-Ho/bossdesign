<?php
require 'db.php';

// 1. 取得文章 ID
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

// 2. 從資料庫抓取文章 (news 表格)
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

// 如果找不到文章，跳回首頁
if (!$article) { echo "<script>alert('找不到這篇文章'); window.location.href='index.php';</script>"; exit; }

// 3. 處理圖片路徑
$img = $article['image'] ? "uploads/".$article['image'] : "";
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | BossDesign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&family=Noto+Serif+TC:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Noto Sans TC', sans-serif; color: #555; padding-top: 80px; }
        h1 { font-family: 'Noto Serif TC', serif; font-weight: 700; color: #333; font-size: 2.5rem; margin-bottom: 1rem; }
        
        .navbar { background: rgba(255, 255, 255, 0.95); padding: 16px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .navbar-brand span { color: #2E8B57; }
        .nav-link { color: #444 !important; font-weight: 500; }

        /* ✅ 圖片縮小與置中設定 */
        .article-container { max-width: 900px; margin: 0 auto; padding: 60px 20px; }
        
        .article-image-wrapper {
            text-align: center; /* 讓圖片置中 */
            margin-bottom: 40px;
            background-color: #f9f9f9; /* 淺灰底色，讓透明圖更清楚 */
            border-radius: 12px;
            padding: 20px;
        }

        .article-main-img {
            max-width: 100%;      /* 寬度不超過螢幕 */
            width: auto;          /* 保持原始比例 */
            max-height: 500px;    /* ✅ 高度限制：最大 500px (這樣就不會太巨大) */
            object-fit: contain;  /* 保持圖片完整，不裁切 */
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .article-meta { color: #999; font-size: 0.9rem; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .article-content { font-size: 1.15rem; line-height: 1.9; color: #333; }
        .article-content p { margin-bottom: 25px; }
        
        .btn-back { border-radius: 50px; padding: 8px 25px; border: 1px solid #ddd; background: white; color: #555; text-decoration: none; transition: 0.3s; }
        .btn-back:hover { background: #333; color: white; border-color: #333; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Boss<span>Design</span> | 博斯美學</a>
            <div class="ms-auto">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i>回首頁</a>
            </div>
        </div>
    </nav>

    <div class="article-container">
        <div class="text-center">
            <h1 class="mb-3"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-meta">
                <span class="me-3"><i class="far fa-calendar-alt me-2"></i><?php echo $article['date']; ?></span>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><?php echo htmlspecialchars($article['category']); ?></span>
            </div>
        </div>

        <?php if($img): ?>
        <div class="article-image-wrapper">
            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-main-img">
        </div>
        <?php endif; ?>

        <div class="article-content">
            <?php echo nl2br($article['content']); ?>
        </div>
        
        <div class="mt-5 pt-4 border-top text-center">
            <a href="index.php#contact" class="btn btn-outline-success rounded-pill px-5 py-2">對這個議題感興趣？聯絡我們</a>
        </div>
    </div>

</body>
</html>