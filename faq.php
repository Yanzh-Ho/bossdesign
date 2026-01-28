<?php
require 'db.php';

// 1. 連線資料庫 (修正：改成 faqs 有加 s)
try {
    $sql = "SELECT * FROM faqs ORDER BY sort ASC, created_at DESC"; // 加入排序功能
    $stmt = $pdo->query($sql);
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $faqs = [];
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>常見問題 | BossDesign 博斯美學</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&family=Noto+Serif+TC:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================
           1. 全域變數與重置 (跟 projects.php 一致)
           ========================================= */
        :root {
            --primary-color: #2E8B57;   
            --footer-bg: #2F3A34;
            --footer-text: #E6EAE7;
            --text-dark: #2c3e50;
            --text-gray: #5f6368;       
            --radius-btn: 50px;
            --radius-card: 16px;
            --shadow-soft: 0 10px 40px rgba(0,0,0,0.06);
            --shadow-hover: 0 20px 50px rgba(0,0,0,0.12);
        }

        body, p, li, a, span, div, input, button, select, textarea {
            font-family: 'Noto Sans TC', sans-serif !important;
            color: var(--text-gray);
            line-height: 1.8;
            font-weight: 400;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand, .q-tag { 
            font-family: 'Noto Serif TC', serif !important;
            font-weight: 700; 
            color: var(--text-dark); 
            letter-spacing: 0.02em; 
        }

        body { padding-top: 80px; background-color: #ffffff; }

        /* =========================================
           2. 導覽列 (跟 projects.php 一致)
           ========================================= */
        .navbar { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); padding: 16px 0; transition: 0.4s; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .navbar.scrolled { padding: 10px 0; background: rgba(255, 255, 255, 0.98); box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 1.6rem; }
        .navbar-brand span { color: var(--primary-color); }
        .nav-link { color: #444 !important; font-weight: 500; margin: 0 12px; font-size: 0.95rem; letter-spacing: 0.5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--primary-color) !important; }
        .dropdown-menu { border: none; border-radius: 16px; box-shadow: var(--shadow-soft); padding: 10px; margin-top: 15px; min-width: 200px; }
        .dropdown-item { padding: 10px 20px; font-size: 0.95rem; border-radius: 8px; transition: 0.2s; }
        .dropdown-item:hover { background-color: rgba(46, 139, 87, 0.08); color: var(--primary-color); }
        .btn-nav-cta { background-color: transparent !important; border: 1px solid #333; color: #333 !important; border-radius: 50px; padding: 8px 28px; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; }
        .btn-nav-cta:hover { background-color: #333 !important; color: #fff !important; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        /* =========================================
           3. Page Header (跟 projects.php 一致)
           ========================================= */
        .page-header {
            background-image: url('首頁/草.jpg'); 
            background-size: cover; background-position: center; background-attachment: fixed; height: 400px; display: flex; align-items: center; justify-content: center; text-align: center; color: white; position: relative; margin-bottom: 80px;
        }
        .page-header::before { content: ''; position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6)); }
        .header-content { position: relative; z-index: 2; padding: 0 20px; }
        .page-header h1 { font-size: 3rem; margin-bottom: 20px; color: white !important; text-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .page-header p { font-size: 1.2rem; opacity: 0.95; font-weight: 300; letter-spacing: 2px; color: white !important; }

        /* =========================================
           4. FAQ 列表樣式 (優化版)
           ========================================= */
        .faq-card {
            background: #fff;
            border: none;
            border-radius: var(--radius-card); /* 圓角一致 */
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); /* 陰影更柔和 */
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .faq-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-soft); }
        
        .accordion-button {
            font-size: 1.15rem;
            font-family: 'Noto Serif TC', serif !important; /* 標題用襯線體 */
            font-weight: 600;
            color: var(--text-dark);
            padding: 25px 30px;
            background: #fff;
            border: none;
            box-shadow: none !important;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(46, 139, 87, 0.05); /* 淡淡的綠色背景 */
            color: var(--primary-color);
        }
        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232E8B57'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transform: scale(0.8);
        }
        .accordion-body {
            padding: 10px 30px 40px 30px;
            color: #555;
            line-height: 2;
            font-size: 1.05rem;
            background-color: rgba(46, 139, 87, 0.05); /* 保持背景色一致 */
        }
        .q-tag { 
            color: var(--primary-color); 
            font-weight: 700; 
            margin-right: 15px; 
            font-size: 1.3rem; 
            font-family: 'Noto Serif TC', serif !important;
        }

        /* Footer (跟 projects.php 一致) */
        footer { background: var(--footer-bg); color: var(--footer-text); padding: 90px 0 40px; margin-top: 60px; }
        footer h5 { color: #fff !important; margin-bottom: 30px; font-family: 'Noto Serif TC', serif !important; font-weight: 700; font-size: 1.25rem; }
        .footer-desc { color: #aab2ad; line-height: 1.8; margin-bottom: 30px; }
        .footer-info p { color: #aab2ad; margin-bottom: 12px; font-size: 0.95rem; display: flex; align-items: start; }
        .footer-info p i { margin-top: 5px; width: 20px; color: var(--primary-color); }
        footer a { color: #aab2ad; text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s; }
        footer a:hover { color: #fff; padding-left: 8px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Boss<span>Design</span> | 博斯美學</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">首頁</a></li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAbout" role="button" data-bs-toggle="dropdown" aria-expanded="false">關於我們</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php#company">公司介紹</a></li>
                            <li><a class="dropdown-item" href="about.php#founder">創辦人介紹</a></li>
                            <li><a class="dropdown-item" href="about.php#mission">公司主旨</a></li>
                            <li><a class="dropdown-item" href="about.php#office-reels">空間動態實錄</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="about.php#process">標準化流程</a></li>
                            <li><a class="dropdown-item" href="about.php#system">深度系統整合</a></li>
                            <li><a class="dropdown-item" href="about.php#insights">觀察與洞察</a></li>
                        </ul>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="projects.php">作品</a></li> 
                    <li class="nav-item"><a class="nav-link active" href="faq.php">常見問題</a></li>
                    <li class="nav-item ms-3">
                        <a class="nav-link btn-nav-cta" href="index.php#contact">免費評估</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="header-content">
            <h1>常見問題 Q&A</h1>
            <p>關於設計、流程與預算的專業解答</p>
        </div>
    </header>

    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9"> <?php if(empty($faqs)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">目前沒有常見問題資料。</p>
                        <a href="faq_admin.php" class="btn btn-sm btn-outline-success">去後台新增</a>
                    </div>
                <?php else: ?>
                    <div class="accordion" id="faqAccordion">
                        <?php foreach($faqs as $index => $row): ?>
                            <?php 
                                $headingId = "heading" . $row['id'];
                                $collapseId = "collapse" . $row['id'];
                                $isFirst = ($index === 0) ? "true" : "false"; 
                                $showClass = ($index === 0) ? "show" : ""; 
                                $btnClass = ($index === 0) ? "" : "collapsed";
                            ?>
                            <div class="faq-card">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button <?php echo $btnClass; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="<?php echo $isFirst; ?>" aria-controls="<?php echo $collapseId; ?>">
                                        <span class="q-tag">Q<?php echo $index + 1; ?>.</span> 
                                        <?php echo htmlspecialchars($row['question']); ?>
                                    </button>
                                </h2>
                                <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $showClass; ?>" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <?php echo nl2br(htmlspecialchars($row['answer'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <footer class="py-5" style="background-color: #2F3A34;">
        <div class="container">
            <div class="row align-items-start">
                
                <div class="col-lg-4 mb-5">
                    <h5 class="text-white fw-bold mb-4 mt-0">BossDesign 博斯美學</h5>
                    <p class="footer-desc text-white-50">
                        以策略設計為核心，<br>讓辦公空間成為企業永續競爭力的一部分。
                    </p>
                    <div class="footer-info text-white-50">
                        <p class="mb-2"><i class="fas fa-phone-alt me-2 text-success"></i>02-2265-0351</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i>台北市信義區信義路五段5號3樓C40</p>
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i>台中市西屯區工業區一路96-3號</p>
                        <p class="mb-2"><i class="far fa-clock me-2 text-success"></i>週一至週五 9:00 – 18:00</p>
                    </div>
                </div>
                
                <div class="col-lg-2 mb-5">
                    <h5 class="text-white fw-bold mb-4 mt-0">連結</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="index.php" class="text-white-50 text-decoration-none">首頁</a></li>
                        <li class="mb-2"><a href="about.php" class="text-white-50 text-decoration-none">關於我們</a></li>
                        <li class="mb-2"><a href="projects.php" class="text-white-50 text-decoration-none">作品</a></li>
                        <li class="mb-2"><a href="faq.php" class="text-white-50 text-decoration-none">常見問題</a></li>
                        <li class="mb-2"><a href="index.php#contact" class="text-white-50 text-decoration-none">聯絡我們</a></li>
                        <li class="mb-2 mt-3"><a href="login.php" class="text-white-50 text-decoration-none"><i class="fas fa-user-lock me-1"></i> 管理員登入</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 mb-5">
                    <h5 class="text-white fw-bold mb-4 mt-0">Facebook</h5>
                    <div style="background: white; border-radius: 8px; overflow: hidden; height: 200px;">
                        <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FJEB.TW%3Flocale%3Dzh_TW&tabs=timeline&width=100&height=200&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId&tabs=timeline&width=300&height=200&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=false&appId" width="100%" height="200" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                    </div>
                </div>

                <div class="col-lg-3 mb-5">
                    <h5 class="text-white fw-bold mb-4 mt-0">YouTube</h5>
                    <div style="border-radius: 8px; overflow: hidden; height: 200px; background: #000;">
                        <iframe width="100%" height="200" src="https://www.youtube.com/embed/_bcZE2h2KHk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary mt-4 pt-4 text-center">
                <p class="small text-white-50 mb-0">&copy;BossDesign. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) { navbar.classList.add('scrolled'); } else { navbar.classList.remove('scrolled'); }
        });
    </script>
</body>
</html>