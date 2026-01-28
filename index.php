<?php
require 'db.php';
// 讀取首頁資料
$carousels = $pdo->query("SELECT * FROM home_carousel ORDER BY sort ASC")->fetchAll();
$stats = $pdo->query("SELECT * FROM home_stats ORDER BY sort ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BossDesign 博斯美學 | 建造會呼吸的辦公室</title>
    <meta name="description" content="博斯美學 提供 ESG 辦公室解決方案，結合聲學、光環境與靈活隔間，為企業打造可持續、高效率的工作生態系統。">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Noto+Sans+TC:wght@300;400;500;700&family=Noto+Serif+TC:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <style>
        :root { --primary-color: #2E8B57; --secondary-color: #4169E1; --accent-color: #FF8C00; --footer-bg: #2F3A34; --footer-text: #E6EAE7; --text-dark: #2c3e50; --text-gray: #5f6368; --radius-btn: 50px; --radius-card: 20px; }
        body { font-family: 'Noto Sans TC', sans-serif !important; color: var(--text-gray); padding-top: 80px; overflow-x: hidden; margin: 0; font-weight: 400; }
        h1, h2, h3, h4, h5, h6, .navbar-brand { font-family: 'Noto Serif TC', serif !important; font-weight: 700; color: var(--text-dark); letter-spacing: 0.02em; }
        .stat-number, .hero-eyebrow, .blog-meta, .post-meta { font-family: 'Inter', sans-serif !important; }
        .navbar { transition: 0.4s; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); padding: 16px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .navbar.scrolled { padding: 10px 0 !important; background: rgba(255, 255, 255, 0.98); box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 1.6rem; color: var(--text-dark) !important; }
        .navbar-brand span { color: var(--primary-color); }
        .nav-link { color: #444 !important; font-weight: 500; margin: 0 10px; font-size: 0.95rem; letter-spacing: 0.5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--primary-color) !important; }
        .dropdown-menu { border: none; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); padding: 10px; margin-top: 15px; }
        .dropdown-item { padding: 10px 20px; color: #666; border-radius: 8px; transition: 0.2s; }
        .dropdown-item:hover { background-color: rgba(46, 139, 87, 0.08); color: var(--primary-color); }
        .btn-nav-cta { background-color: transparent !important; border: 1px solid #333; color: #333 !important; border-radius: 50px; padding: 8px 28px; font-size: 0.9rem; font-weight: 500; transition: all 0.3s ease; }
        .btn-nav-cta:hover { background-color: #333 !important; color: #fff !important; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .carousel-item { height: 90vh; min-height: 600px; background-color: #000; position: relative; }
        .hero-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; transform: scale(1); transition: transform 6s ease-in-out; }
        .carousel-item.active .hero-bg { transform: scale(1.1); }
        .carousel-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6)); }
        .carousel-caption { bottom: 30%; left: 10%; right: 10%; text-align: left; }
        .hero-eyebrow { color: var(--accent-color); font-size: 0.9rem; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 15px; display: inline-block; text-shadow: 0 2px 4px rgba(0,0,0,0.8); opacity: 0; animation: fadeInUp 1s ease forwards 0.2s; }
        .carousel-caption h1 { font-size: 3.5rem; color: #fff !important; text-shadow: 0 4px 20px rgba(0,0,0,0.5); margin-bottom: 25px; opacity: 0; animation: fadeInUp 1s ease forwards 0.5s; }
        .carousel-caption .lead { font-size: 1.5rem; color: #f8f9fa; opacity: 0; animation: fadeInUp 1s ease forwards 0.8s; }
        .carousel-caption .btn { opacity: 0; animation: fadeInUp 1s ease forwards 1.1s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .btn-hero-transparent { padding: 15px 45px; font-size: 1.2rem; font-weight: bold; border-radius: var(--radius-btn); transition: all 0.3s ease; background-color: transparent; color: #ffffff !important; border: 2px solid #ffffff; }
        .btn-hero-transparent:hover { background-color: #ffffff; color: var(--primary-color) !important; border-color: #ffffff; transform: translateY(-3px); }
        .stats-section { background-color: white; color: var(--text-dark); padding: 100px 0; border-bottom: 1px solid #eee; }
        .stat-number { font-size: 4rem; font-weight: 700; color: var(--accent-color); margin-bottom: 10px; line-height: 1; display: inline-flex; align-items: baseline; }
        .stat-unit, .stat-prefix { font-size: 0.5em; color: var(--accent-color); font-weight: 500; }
        .stat-unit { margin-left: 5px; } .stat-prefix { margin-right: 5px; }
        .stat-label { font-size: 1.1rem; color: #666; font-weight: 500; }
        .section-padding { padding: 120px 0; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header h6 { color: var(--primary-color); letter-spacing: 2px; font-weight: 700; margin-bottom: 15px; }
        .section-header h2 { font-size: 2.5rem; font-weight: 700; color: var(--text-dark); }
        .post-card-wrapper { display: block; text-decoration: none; color: inherit; margin-bottom: 40px; transition: transform 0.3s ease; }
        .post-card-wrapper:hover { transform: translateY(-8px); }
        .post-img-container { border-radius: 24px; overflow: hidden; background-color: #f0f0f0; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); position: relative; display: flex; align-items: center; justify-content: center; }
        .post-img-container img { width: 100%; height: auto; display: block; transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
        .post-card-wrapper:hover .post-img-container img { transform: scale(1.05); }
        .post-content { padding: 0 5px; }
        .post-title { font-size: 1.4rem; font-weight: 700 !important; color: var(--primary-color); margin-bottom: 8px; line-height: 1.4; transition: color 0.2s; }
        .post-card-wrapper:hover .post-title { color: #236c43; }
        .post-meta { font-size: 0.95rem; color: #888; display: flex; align-items: center; }
        .meta-separator { margin: 0 12px; color: #ddd; }
        .meta-icon { margin-right: 6px; color: var(--primary-color); opacity: 0.7; }
        .contact-section-premium { background-image: url('首頁/表單.jpg'); background-size: cover; background-position: center; background-attachment: fixed; padding: 120px 0; position: relative; } 
        .contact-overlay-premium { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); }
        .contact-container { position: relative; z-index: 2; }
        .form-control-premium { background: transparent; border: none; border-bottom: 1px solid rgba(255, 255, 255, 0.5); border-radius: 0; color: #fff !important; padding: 15px 0; font-size: 1.1rem; }
        .form-control-premium:focus { background: transparent; box-shadow: none; border-bottom-color: var(--primary-color); color: #fff !important; }
        .form-control-premium::placeholder { color: rgba(255, 255, 255, 0.7); }
        select.form-control-premium { color: #fff !important; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0px center; background-size: 16px; cursor: pointer; }
        select.form-control-premium option { background-color: #333; color: #fff; }
        .btn-submit-premium { background: transparent; color: #fff; border: 2px solid var(--primary-color); padding: 15px 50px; font-size: 1.2rem; font-weight: 600; border-radius: 50px; transition: 0.4s; margin-top: 20px; width: 100%; }
        .btn-submit-premium:hover { background: #fff; color: var(--primary-color); border-color: #fff; transform: translateY(-3px); box-shadow: 0 0 15px rgba(255, 255, 255, 0.3); }
        footer { background: var(--footer-bg); color: var(--footer-text); padding: 80px 0 30px; }
        footer h5 { color: #FFFFFF !important; margin-bottom: 25px; font-weight: 700; }
        .footer-desc { color: #C9D1CB; line-height: 1.8; margin-bottom: 30px; }
        .footer-info p { color: #C9D1CB; margin-bottom: 8px; font-size: 0.95rem; }
        footer a { color: #A9B4AE; text-decoration: none; display: block; margin-bottom: 10px; transition: 0.3s; }
        footer a:hover { color: #fff; padding-left: 5px; }
        .footer-bottom { font-size: 0.85rem; color: #9FAAA4; text-align: center; }
        .sticky-action-bar { position: fixed; bottom: 30px; right: 30px; display: flex; flex-direction: column; gap: 12px; z-index: 9999; }
        .action-btn { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; text-decoration: none; background-color: rgba(44, 62, 80, 0.8); backdrop-filter: blur(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); transition: 0.3s; }
        .action-btn:hover { background-color: var(--primary-color); transform: translateY(-3px) scale(1.05); }
        .top-btn { opacity: 0; visibility: hidden; transform: translateY(10px); }
        .top-btn.show { opacity: 1; visibility: visible; transform: translateY(0); }
        @media (max-width: 768px) { .stats-section { padding: 50px 0; background: #fafafa; } .section-header h2 { font-size: 2rem; } .stat-number { font-size: 2.2rem !important; } .section-padding { padding: 60px 0; } .carousel-caption h1 { font-size: 2rem !important; } .btn-hero-transparent { padding: 10px 30px; font-size: 1rem; } body { padding-top: 70px; } }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Boss<span>Design</span> | 博斯美學</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">首頁</a></li>
                    
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
                    <li class="nav-item"><a class="nav-link" href="faq.php">常見問題</a></li>
                    <li class="nav-item ms-3">
                        <a class="nav-link btn-nav-cta" href="#contact">免費評估</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php foreach($carousels as $index => $row): ?>
                <button type="button" data-bs-target="#home" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index==0?'active':''; ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach($carousels as $index => $row): 
                $img = strpos($row['image'], '首頁/') === 0 ? $row['image'] : "uploads/".$row['image'];
            ?>
            <div class="carousel-item <?php echo $index==0?'active':''; ?>">
                <div class="hero-bg" style="background-image: url('<?php echo $img; ?>');"></div>
                <div class="carousel-overlay"></div>
                <div class="carousel-caption">
                    <div class="container">
                        <h1><?php echo htmlspecialchars($row['title']); ?></h1>
                        <p class="lead"><?php echo nl2br(htmlspecialchars($row['subtitle'])); ?></p>
                        <a href="<?php echo ($index==0 ? '#contact' : ($index==1 ? 'about.php#process' : 'about.php#system')); ?>" class="btn btn-hero-transparent mt-4">
                            <?php echo ($index==0 ? '獲取 ESG 辦公室評估報告' : ($index==1 ? '查看標準化流程' : '了解設計細節')); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#home" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#home" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </header>

    <section class="stats-section">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h6 style="color: var(--primary-color); letter-spacing: 2px; font-weight: 700;">REAL IMPACT</h6>
                    <h3 class="fw-bold mb-3" style="color: #333;">設計不只是美學，更是可衡量的企業資產</h3>
                    <p class="text-muted">我們透過數據追蹤每一個設計決策的成效，確保您的投入能轉化為具體的 ESG 指標與營運優勢。</p>
                </div>
            </div>

            <div class="row text-center">
                <?php foreach($stats as $row): ?>
                <div class="col-md-3 col-6 mb-5 mb-md-0">
                    <div class="stat-number" data-target="<?php echo $row['number']; ?>" data-unit="<?php echo $row['unit']; ?>" data-prefix="<?php echo $row['prefix']; ?>">0</div>
                    <div class="stat-label"><?php echo $row['label']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h6>Blog</h6>
                <h2>部落格</h2>
            </div>
            
            <div id="latest-posts-container" class="row">
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-spinner fa-spin me-2"></i> 正在載入最新消息...
                </div>
            </div>

        </div>
    </section>

    <section id="contact" class="contact-section-premium">
        <div class="contact-overlay-premium"></div> 
        <div class="container contact-container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center text-white mb-5">
                        <h2 class="fw-bold mb-3" style="color: #ffffff;">您的辦公室，準備好迎接未來了嗎？</h2>
                        <p class="opacity-75" style="color: #dddddd;">請留下您的需求，我們的設計顧問將為您提供免費的空間策略評估。</p>
                    </div>
                    
                    <form id="contactForm" method="POST">
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control-premium" placeholder="如何稱呼您？*" required>
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="email" class="form-control-premium" placeholder="公司 Email*" required>
                            </div>
                            <div class="col-md-4">
                                <input type="tel" name="phone" class="form-control-premium" placeholder="聯絡電話">
                            </div>
                        </div>
                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <select name="issue" class="form-control-premium" id="issueSelect" onchange="toggleOtherInput()">
                                    <option value="" disabled selected>您目前最困擾的辦公室問題？</option>
                                    <option value="空間不足">空間不夠用 / 需要擴編</option>
                                    <option value="噪音干擾">噪音干擾 / 會議室不足</option>
                                    <option value="裝修老舊">裝修老舊 / 風格過時</option>
                                    <option value="ESG需求">希望導入 ESG / 永續設計</option>
                                    <option value="其他">其他 (請說明)</option>
                                </select>
                                <div id="otherInputDiv" style="display: none; margin-top: 15px;">
                                    <input type="text" name="issue_other" class="form-control-premium" placeholder="請具體描述您的需求...">
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-8">
                                <input type="text" name="message" class="form-control-premium" placeholder="備註訊息 (例如：預計坪數、預算範圍...)">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn-submit-premium">
                                    索取評估報告 <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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

    <div class="sticky-action-bar">
        <a href="https://line.me/ti/p/fSAK-T32jT" target="_blank" class="action-btn" title="加 LINE 諮詢"><i class="fab fa-line"></i></a>
        <a href="tel:0222650351" class="action-btn" title="撥打電話"><i class="fas fa-phone-alt"></i></a>
        <button id="backToTop" class="action-btn top-btn" title="回到頂部"><i class="fas fa-arrow-up"></i></button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ✅ 1. 強制啟動輪播
        var myCarousel = document.querySelector('#home')
        var carousel = new bootstrap.Carousel(myCarousel, {
            interval: 5000,
            ride: 'carousel'
        })

        // 2. 導覽列與回到頂部效果
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            const backToTopBtn = document.getElementById('backToTop');
            
            if (window.scrollY > 50) { navbar.classList.add('scrolled'); } else { navbar.classList.remove('scrolled'); }
            
            if (window.scrollY > 300) { backToTopBtn.classList.add('show'); } else { backToTopBtn.classList.remove('show'); }
        });
        
        const backToTopBtn = document.getElementById('backToTop');
        backToTopBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });

        // ✅ 4. 數字捲動特效
        const statsSection = document.querySelector('.stats-section');
        const statNumbers = document.querySelectorAll('.stat-number');
        let started = false; 

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !started) {
                started = true;
                statNumbers.forEach(stat => {
                    const target = +stat.getAttribute('data-target'); 
                    const unit = stat.getAttribute('data-unit') || ''; 
                    const prefix = stat.getAttribute('data-prefix') || '';
                    const duration = 2000; 
                    const stepTime = 20;   
                    const steps = duration / stepTime;
                    const increment = target / steps;
                    
                    let current = 0;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        
                        stat.innerHTML = `
                            <span class="stat-prefix small">${prefix}</span>
                            ${Math.floor(current).toLocaleString()}
                            <span class="stat-unit small">${unit}</span>
                        `;
                    }, stepTime);
                });
            }
        }, { threshold: 0.1 }); // 改成 0.1 比較容易觸發

        if (statsSection) {
            observer.observe(statsSection);
        }

        // 3. 渲染最新消息
        function renderPosts(data) {
            const container = document.getElementById('latest-posts-container');
            container.innerHTML = '';

            let html = '';
            
            data.forEach(post => {
                let imgUrl = 'https://placehold.co/600x400?text=No+Image';
                if (post.image && post.image.trim() !== '') {
                    imgUrl = 'uploads/' + post.image;
                }

                let contentText = post.content ? post.content.replace(/<[^>]+>/g, '') : '';
                let summary = contentText.substring(0, 40) + '...';

                html += `
                    <div class="col-lg-4 col-md-6 mb-5">
                        <a href="article.php?id=${post.id}" class="post-card-wrapper">
                            <div class="post-img-container">
                                <img src="${imgUrl}" alt="${post.title}">
                            </div>
                            <div class="post-content">
                                <h4 class="post-title">${post.title}</h4>
                                <div class="post-meta">
                                    <i class="fas fa-map-marker-alt meta-icon"></i>${post.date} 
                                    <span class="meta-separator">|</span>
                                    <i class="fas fa-vector-square meta-icon"></i>${post.category} 
                                </div>
                                <p class="text-muted mt-2 small">${summary}</p>
                            </div>
                        </a>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        fetch('get_news.php')
            .then(response => response.json())
            .then(data => {
                renderPosts(data);
            })
            .catch(error => console.error('連線失敗:', error));
    </script>
</body>
</html>