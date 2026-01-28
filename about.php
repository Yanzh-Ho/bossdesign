<?php
require 'db.php';
$company = $pdo->query("SELECT * FROM company_info WHERE id=1")->fetch();
$about_img = ($company['about_image']) ? "uploads/" . $company['about_image'] : "富邦/富邦3.jpg";
$founder_img = ($company['founder_image']) ? "uploads/" . $company['founder_image'] : "首頁/丁董1.jpg";

// 抓取新的區塊資料
$philosophy = $pdo->query("SELECT * FROM about_philosophy ORDER BY sort ASC")->fetchAll();
$workflow = $pdo->query("SELECT * FROM about_workflow ORDER BY sort ASC")->fetchAll();
$system = $pdo->query("SELECT * FROM about_system ORDER BY sort ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>關於我們 | BossDesign 博斯美學</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Noto+Sans+TC:wght@300;400;500;700&family=Noto+Serif+TC:wght@600;700&display=swap" rel="stylesheet">
    <style>
        /* (這裡的 CSS 保持原樣，與上一版相同) */
        :root { --primary-color: #2E8B57; --footer-bg: #2F3A34; --footer-text: #E6EAE7; --text-dark: #2c3e50; --text-gray: #5f6368; --radius-btn: 50px; --radius-card: 20px; --shadow-soft: 0 10px 40px rgba(0,0,0,0.06); --shadow-hover: 0 20px 50px rgba(0,0,0,0.12); }
        body, p, li, a, span, div, input, button, select, textarea { font-family: 'Noto Sans TC', sans-serif !important; color: var(--text-gray); line-height: 1.8; font-weight: 400; }
        h1, h2, h3, h4, h5, h6, .navbar-brand, .founder-quote-premium { font-family: 'Noto Serif TC', serif !important; font-weight: 700; color: var(--text-dark) !important; letter-spacing: 0.02em; }
        body { padding-top: 80px; background-color: #fff; overflow-x: hidden; }
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
        .page-header { background-image: url('o/o8.JPG'); background-size: cover; background-position: center; background-attachment: fixed; height: 400px; display: flex; align-items: center; justify-content: center; text-align: center; position: relative; margin-bottom: 80px; }
        .page-header::before { content: ''; position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7)); }
        .page-header-content { position: relative; z-index: 2; padding: 0 20px; }
        .page-header h1 { font-size: 3rem; margin-bottom: 20px; color: #ffffff !important; text-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .page-header p { font-size: 1.2rem; opacity: 0.95; font-weight: 300; letter-spacing: 2px; color: #ffffff !important; }
        .section-padding { padding: 120px 0; }
        .section-header { text-align: center; margin-bottom: 70px; }
        .section-header h6 { color: var(--primary-color); letter-spacing: 2px; font-weight: 700; margin-bottom: 15px; font-family: 'Inter', sans-serif !important; }
        .section-header h2 { font-size: 2.5rem; margin-bottom: 15px; }
        .about-img-wrapper { position: relative; border-radius: var(--radius-card); overflow: hidden; box-shadow: var(--shadow-soft); }
        .about-img-wrapper img { width: 100%; transition: 0.6s ease; display: block; }
        .about-img-wrapper:hover img { transform: scale(1.03); }
        .founder-section-premium { padding: 140px 0; background: #fff; }
        .founder-layout-premium { display: flex; align-items: center; gap: 80px; }
        .founder-visual-premium { flex: 1; position: relative; display: flex; justify-content: center; align-items: center; }
        .profile-circle-premium { width: 350px; height: 350px; border-radius: 50%; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); box-shadow: 20px 20px 60px rgba(0,0,0,0.1); position: relative; z-index: 2; }
        .profile-circle-premium img { width: 100%; height: 100%; object-fit: cover; }
        .founder-tag-premium { position: absolute; bottom: 30px; right: 10px; background: var(--text-dark); color: #fff; padding: 8px 24px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; letter-spacing: 2px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); z-index: 3; font-family: 'Inter', sans-serif !important; }
        .vertical-text-deco { position: absolute; top: -20px; left: -20px; writing-mode: vertical-rl; font-family: 'Noto Serif TC', serif; font-size: 3rem; font-weight: 700; color: rgba(0,0,0,0.03); letter-spacing: 0.2em; z-index: 1; user-select: none; }
        .founder-content-premium { flex: 1.3; }
        .founder-quote-premium { font-size: 1.8rem; line-height: 1.5; color: var(--text-dark); margin-bottom: 40px; position: relative; padding-left: 0; }
        .founder-desc-premium { font-size: 1.05rem; line-height: 1.9; color: #666; margin-bottom: 20px; text-align: justify; white-space: pre-line; }
        .founder-signature-premium { margin-top: 40px; }
        .founder-signature-premium h4 { font-size: 1.5rem; margin-bottom: 5px; }
        /* Reels */
        .carousel-container-wrapper { position: relative; max-width: 1300px; margin: 0 auto; padding: 0 60px; }
        .scrolling-wrapper { display: flex; flex-wrap: nowrap; overflow-x: auto; gap: 24px; padding: 20px 5px; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; }
        .scrolling-wrapper::-webkit-scrollbar { display: none; }
        .scrolling-card { flex: 0 0 260px; width: 260px; }
        .nav-arrow-btn { position: absolute; top: 45%; transform: translateY(-50%); width: 48px; height: 48px; border-radius: 50%; background-color: #fff; border: 1px solid rgba(0,0,0,0.1); color: var(--text-dark); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: 0.3s; }
        .nav-arrow-btn:hover { background-color: var(--text-dark); color: #fff; transform: translateY(-50%) scale(1.1); }
        .nav-prev { left: 0; } .nav-next { right: 0; }
        .reel-card { cursor: pointer; transition: transform 0.3s ease; }
        .reel-img-wrapper { position: relative; width: 100%; aspect-ratio: 9/16; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .reel-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
        .reel-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.2); display: flex; justify-content: center; align-items: center; opacity: 0; transition: all 0.3s; }
        .reel-overlay i { font-size: 3.5rem; color: white; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); transform: scale(0.8); transition: 0.3s; }
        .reel-card:hover { transform: translateY(-5px); }
        .reel-card:hover img { transform: scale(1.05); }
        .reel-card:hover .reel-overlay { opacity: 1; background: rgba(0,0,0,0.3); }
        .reel-card:hover .reel-overlay i { transform: scale(1); }
        .reel-caption { margin-top: 15px; text-align: center; font-weight: 500; color: #333; font-size: 1rem; line-height: 1.4; transition: 0.3s; }
        .reel-card:hover .reel-caption { color: var(--primary-color); }
        /* Process & Bento */
        .process-step { text-align: center; padding: 40px 25px; border-radius: 20px; background: #fff; height: 100%; transition: 0.3s; }
        .process-step:hover { transform: translateY(-10px); box-shadow: var(--shadow-hover); }
        .process-icon-wrapper { width: 90px; height: 90px; background: rgba(46, 139, 87, 0.05); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 25px; transition: 0.4s; }
        .process-step:hover .process-icon-wrapper { background: var(--primary-color); color: white; transform: scale(1.1); }
        .bento-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
        .bento-card { position: relative; border-radius: 24px; overflow: hidden; height: 360px; background: #000; }
        .bento-card img { width: 100%; height: 100%; object-fit: cover; opacity: 0.85; transition: 0.6s ease; }
        .bento-card:hover img { opacity: 0.6; transform: scale(1.08); }
        .bento-content { position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); }
        .bento-content h4 { color: white !important; font-size: 1.4rem; margin-bottom: 8px; }
        .bento-content p { color: rgba(255,255,255,0.9) !important; font-size: 0.95rem; margin: 0; }
        .philosophy-section-premium { padding: 120px 0; background-color: #F8F9FA; }
        .philosophy-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .philosophy-card-premium { background: #fff; padding: 60px 40px; border-radius: var(--radius-card); text-align: center; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: 1px solid rgba(0,0,0,0.02); height: 100%; }
        .philosophy-card-premium:hover { transform: translateY(-10px); box-shadow: var(--shadow-hover); }
        .icon-wrapper-premium { width: 80px; height: 80px; background: rgba(46, 139, 87, 0.08); color: var(--primary-color); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 30px; transition: 0.3s; }
        .philosophy-card-premium:hover .icon-wrapper-premium { background: var(--primary-color); color: #fff; transform: rotateY(180deg); }
        .blog-card { background: white; border: none; border-radius: 20px; overflow: hidden; transition: 0.3s; height: 100%; cursor: pointer; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .blog-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
        .blog-body { padding: 35px; }
        .blog-title { font-size: 1.35rem; margin-bottom: 15px; color: var(--text-dark) !important; transition: 0.3s; }
        .blog-card:hover .blog-title { color: var(--primary-color) !important; }
        footer { background: var(--footer-bg); color: var(--footer-text); padding: 90px 0 40px; margin-top: 60px; }
        footer h5 { color: #fff !important; margin-bottom: 30px; font-weight: 700; font-size: 1.25rem; }
        .footer-desc { color: #aab2ad; line-height: 1.8; margin-bottom: 30px; }
        .footer-info p { color: #aab2ad; margin-bottom: 12px; }
        footer a { color: #aab2ad; text-decoration: none; display: block; margin-bottom: 12px; transition: 0.3s; }
        footer a:hover { color: #fff; padding-left: 5px; }
        .sticky-action-bar { position: fixed; bottom: 30px; right: 30px; display: flex; flex-direction: column; gap: 12px; z-index: 9999; }
        .action-btn { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; text-decoration: none; background-color: rgba(44, 62, 80, 0.8); backdrop-filter: blur(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); transition: all 0.3s; }
        .action-btn:hover { background-color: var(--primary-color); transform: translateY(-3px) scale(1.05); }
        .top-btn { opacity: 0; visibility: hidden; transform: translateY(10px); }
        .top-btn.show { opacity: 1; visibility: visible; transform: translateY(0); }
        #customModal.custom-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.95); display: none; justify-content: center; align-items: center; z-index: 10000; backdrop-filter: blur(5px); }
        #customModal.custom-modal.active { display: flex; animation: fadeInModal 0.3s; }
        @keyframes fadeInModal { from { opacity: 0; } to { opacity: 1; } }
        #customModal .modal-content { background: #111; width: 90%; max-width: 450px; border-radius: 20px; overflow: hidden; border: 1px solid #333; }
        #customModal .modal-body { padding: 0; position: relative; }
        #customModal .close-btn { position: absolute; top: 15px; right: 15px; width: 35px; height: 35px; border-radius: 50%; background: rgba(0,0,0,0.5); color: #fff; border: none; font-size: 20px; z-index: 20; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        #customModal .video-wrap { position: relative; width: 100%; padding-top: 177.77%; background: #000; }
        #customModal .video-wrap iframe { position: absolute; inset: 0; width: 100%; height: 100%; }
        @media (max-width: 992px) { 
            .bento-grid { grid-template-columns: repeat(2, 1fr); }
            .philosophy-grid { grid-template-columns: 1fr; max-width: 450px; margin: 0 auto; }
            .founder-layout-premium { flex-direction: column; gap: 40px; text-align: center; }
            .profile-circle-premium { width: 280px; height: 280px; margin: 0 auto; }
            .founder-quote-premium { font-size: 1.5rem; text-align: center; }
            .founder-desc-premium { text-align: left; }
            .vertical-text-deco { display: none; }
            .page-header h1 { font-size: 2.5rem; }
        }
        @media (max-width: 768px) { 
            body { padding-top: 60px; } 
            .section-header h2 { font-size: 2rem; } 
            .bento-grid { grid-template-columns: 1fr; } 
            .bento-card { height: 280px; }
            .carousel-container-wrapper { padding: 0 15px; max-width: 100%; }
            .nav-arrow-btn { display: none; } 
            .scrolling-card { flex: 0 0 160px; width: 160px; }
            .founder-section-premium { padding: 80px 0; }
        }
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
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownAbout" role="button" data-bs-toggle="dropdown" aria-expanded="false">關於我們</a>
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
                        <a class="nav-link btn-nav-cta" href="index.php#contact">免費評估</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="page-header-content">
            <h1>我們定義未來的生存方式</h1>
            <p>不只是設計公司，更是企業永續發展的空間策略夥伴。</p>
        </div>
    </header>

    <section id="company" class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="about-img-wrapper">
                        <img src="<?php echo $about_img; ?>" alt="About BossDesign">
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <h6 class="text-uppercase text-success mb-3" style="letter-spacing: 2px;">WHO WE ARE</h6>
                    <h2 class="mb-4"><?php echo htmlspecialchars($company['about_title']); ?></h2>
                    <p class="text-muted mb-4" style="white-space: pre-line;"><?php echo htmlspecialchars($company['about_desc']); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section id="mission" class="philosophy-section-premium">
        <div class="container">
            <div class="section-header">
                <h6>Our Philosophy</h6>
                <h2>設計核心主旨</h2>
                <p class="text-muted">我們不只設計空間，更設計行為</p>
            </div>
            
            <div class="philosophy-grid">
                <?php foreach($philosophy as $item): ?>
                <div class="philosophy-card-premium">
                    <div class="icon-wrapper-premium"><i class="<?php echo $item['icon']; ?>"></i></div>
                    <h4 class="philosophy-title-premium"><?php echo $item['title']; ?></h4>
                    <p class="philosophy-desc-premium"><?php echo $item['desc_text']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="founder" class="founder-section-premium">
        <div class="container">
            <div class="founder-layout-premium">
                <div class="founder-visual-premium">
                    <div class="profile-circle-premium">
                        <img src="<?php echo $founder_img; ?>" alt="Founder">
                    </div>
                    <div class="founder-tag-premium">FOUNDER</div>
                </div>

                <div class="founder-content-premium">
                    <div class="founder-quote-premium">
                        <?php echo htmlspecialchars($company['founder_quote']); ?>
                    </div>
                    
                    <p class="founder-desc-premium"><?php echo htmlspecialchars($company['founder_desc']); ?></p>

                    <div class="founder-signature-premium">
                        <h4><?php echo htmlspecialchars($company['founder_name']); ?></h4>
                        <span class="text-success small fw-bold fw-normal" style="letter-spacing: 1px;"><?php echo htmlspecialchars($company['founder_title']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="office-reels" class="section-padding">
        <div class="container">
            <div class="section-header">
                <h6>Office Moments</h6>
                <h2>空間動態實錄</h2>
                <p class="text-muted">透過短影音，更真實地感受空間的流動與細節</p>
            </div>

            <div class="carousel-container-wrapper">
                <button class="nav-arrow-btn nav-prev" onclick="scrollContainer('shorts-container', -1)"><i class="fas fa-chevron-left"></i></button>
                
                <div id="shorts-container" class="scrolling-wrapper">
                    <div class="text-center w-100 text-muted p-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="mt-2">正在載入影片...</p>
                    </div>
                </div>
                
                <button class="nav-arrow-btn nav-next" onclick="scrollContainer('shorts-container', 1)"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <section id="process" class="section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h6>Workflow</h6>
                <h2>標準化服務流程</h2>
                <p class="text-muted">從評估到落地，四步驟打造韌性空間</p>
            </div>
            <div class="row">
                <?php foreach($workflow as $item): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step process-arrow">
                        <div class="process-icon-wrapper"><i class="<?php echo $item['icon']; ?>"></i></div>
                        <h4><?php echo $item['title']; ?></h4>
                        <p><?php echo $item['desc_text']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="system" class="section-padding">
        <div class="container">
            <div class="section-header">
                <h6>System Integration</h6>
                <h2>深度系統整合</h2>
                <p class="text-muted">科技賦能，讓設計有憑有據</p>
            </div>
            <div class="bento-grid">
                <?php foreach($system as $item): 
                    $sys_img = $item['image'] ? "uploads/".$item['image'] : "https://placehold.co/400x300";
                ?>
                <div class="bento-card">
                    <img src="<?php echo $sys_img; ?>" alt="<?php echo $item['title']; ?>">
                    <div class="bento-content">
                        <h4><?php echo $item['title']; ?></h4>
                        <p><?php echo $item['desc_text']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>



    <footer class="py-5" style="background-color: #2F3A34;">
        <div class="container">
            <div class="row align-items-start">
                <div class="col-lg-4 mb-5">
                    <h5 class="text-white fw-bold mb-4 mt-0">BossDesign 博斯美學</h5>
                    <p class="footer-desc text-white-50">以策略設計為核心，<br>讓辦公空間成為企業永續競爭力的一部分。</p>
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

    <div id="customModal" class="custom-modal" onclick="closeModal(event)">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">×</button>
            <div class="modal-body">
                <div class="video-wrap">
                    <iframe id="reelFrame" src="" title="Reel Video" frameborder="0" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="p-3">
                    <h5 class="text-white mb-2" style="font-family:'Noto Sans TC';">空間細節說明</h5>
                    <p class="text-white-50 small mb-0">點擊播放按鈕即可開始觀看影片。</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 導覽列與回到頂部
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            const backToTopBtn = document.getElementById('backToTop');
            
            if (window.scrollY > 50) { 
                navbar.classList.add('scrolled'); 
            } else { 
                navbar.classList.remove('scrolled'); 
            }

            if (window.scrollY > 300) { 
                backToTopBtn.classList.add('show'); 
            } else { 
                backToTopBtn.classList.remove('show'); 
            }
        });
        
        const backToTopBtn = document.getElementById('backToTop');
        backToTopBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });

        // 🔥 通用滑動功能
        function scrollContainer(containerId, direction) {
            const container = document.getElementById(containerId);
            const card = container.querySelector('.scrolling-card');
            if(card) {
                const scrollAmount = card.clientWidth + 24; 
                container.scrollBy({
                    left: scrollAmount * direction,
                    behavior: 'smooth'
                });
            }
        }

        // Modal 影片邏輯
        const modal = document.getElementById('customModal');
        const reelFrame = document.getElementById('reelFrame');

        function openModal(rawUrl) {
            let videoId = '';
            if (rawUrl) {
                if (rawUrl.includes('/shorts/')) {
                    videoId = rawUrl.split('/shorts/')[1].split('?')[0];
                } else if (rawUrl.includes('youtu.be/')) {
                    videoId = rawUrl.split('youtu.be/')[1].split('?')[0];
                } else if (rawUrl.includes('v=')) {
                    videoId = rawUrl.split('v=')[1].split('&')[0];
                }
            }
            if (videoId) {
                const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&playsinline=1&rel=0`;
                reelFrame.src = embedUrl;
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(e) {
            if (!e || e.target === modal || e.target.classList.contains('close-btn')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(() => { reelFrame.src = ''; }, 300);
            }
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // ✅ 自動載入後台影片
        document.addEventListener('DOMContentLoaded', function() {
            fetch('get_about_reels.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('shorts-container');
                    
                    if (data.length === 0) {
                        // 如果沒有資料，顯示預設提示
                        container.innerHTML = '<div class="text-muted p-4">目前沒有影片</div>';
                        return;
                    }

                    container.innerHTML = ''; // 清空

                    data.forEach(item => {
                        let imgUrl = item.image ? `uploads/${item.image}` : 'https://placehold.co/260x460?text=No+Image';
                        
                        const html = `
                            <div class="scrolling-card">
                                <div class="reel-card" onclick="openModal('${item.youtube_url}')">
                                    <div class="reel-img-wrapper">
                                        <img src="${imgUrl}" alt="${item.title}">
                                        <div class="reel-overlay"><i class="fas fa-play"></i></div>
                                    </div>
                                    <div class="reel-caption">${item.title}</div>
                                </div>
                            </div>
                        `;
                        container.innerHTML += html;
                    });
                })
                .catch(err => console.error('影片載入失敗:', err));
        });
    </script>
</body>
</html>