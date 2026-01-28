<?php
require 'db.php';

// 1. 直接在後端抓取所有作品資料
try {
    // 依照 id 新到舊排序
    // 依照 sort 小到大排序 (sort 1 排在 sort 2 前面)
$sql = "SELECT * FROM projects ORDER BY sort ASC, created_at DESC";
    $stmt = $pdo->query($sql);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $projects = [];
    // 在正式環境通常不顯示錯誤給使用者，這邊為了除錯先留著
    // echo "讀取失敗：" . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>精選作品 | BossDesign 博斯美學</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&family=Noto+Serif+TC:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================
           1. 全域變數與重置
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

        h1, h2, h3, h4, h5, h6, .navbar-brand, .modal-project-title { 
            font-family: 'Noto Serif TC', serif !important;
            font-weight: 700; 
            color: var(--text-dark); 
            letter-spacing: 0.02em; 
        }

        body { padding-top: 80px; background-color: #ffffff; }

        /* =========================================
           2. 導覽列
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
           3. Page Header
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
           4. 作品卡片樣式
           ========================================= */
        .project-item-clean { margin-bottom: 50px; cursor: pointer; transition: transform 0.3s ease; }
        .project-item-clean:hover { transform: translateY(-8px); }
        .img-wrapper { border-radius: var(--radius-card); overflow: hidden; aspect-ratio: 4/3; background: #f5f5f5; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; }
        .img-wrapper::after { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s; }
        .project-item-clean:hover .img-wrapper::after { opacity: 1; }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
        .project-item-clean:hover .img-wrapper img { transform: scale(1.08); }
        .project-info { padding: 0 5px; }
        .project-title { font-size: 1.4rem; font-weight: 700 !important; color: var(--text-dark); margin-bottom: 8px; line-height: 1.3; transition: color 0.3s; }
        .project-item-clean:hover .project-title { color: var(--primary-color); }
        .project-meta { font-size: 0.9rem; color: #888; display: flex; align-items: center; }
        .meta-divider { margin: 0 10px; color: #ddd; }
        .project-meta i { margin-right: 6px; color: var(--primary-color); opacity: 0.7; }

        /* =========================================
           5. 彈跳視窗與其他
           ========================================= */
        .modal-content { border-radius: 20px; overflow: hidden; border: none; }
        .modal-body { padding: 0; }
        
        .project-carousel-img { 
            width: 100%; 
            height: 60vh; 
            min-height: 400px; 
            object-fit: cover; 
            background-color: #fff;
        }
        
        .project-details-container { padding: 50px; background: #fff; height: 100%; overflow-y: auto; }
        .modal-project-title { font-size: 2.2rem; margin-bottom: 15px; }
        .modal-project-meta { border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; color: #666; }
        .modal-project-desc { line-height: 1.9; font-size: 1.1rem; text-align: justify; color: #555; white-space: pre-line; }

        /* Footer */
        footer { background: var(--footer-bg); color: var(--footer-text); padding: 90px 0 40px; margin-top: 60px; }
        footer h5 { color: #fff !important; margin-bottom: 30px; font-weight: 700; font-size: 1.25rem; }
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

                    <li class="nav-item"><a class="nav-link active" href="projects.php">作品</a></li> 
                    <li class="nav-item"><a class="nav-link" href="faq.php">常見問題</a></li>
                    <li class="nav-item ms-3">
                        <a class="nav-link btn-nav-cta" href="index.php#contact">免費評估</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="header-content">
            <h1>精選商辦案例</h1>
            <p>為企業打造高效、靈活且具品牌識別的辦公環境</p>
        </div>
    </header>

    <section class="container mb-5">
        <div class="row gx-lg-5 gy-5" id="projects-container">
            <?php if (empty($projects)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">目前沒有作品。</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $index => $p): 
                    $coverImg = !empty($p['image']) ? "uploads/" . $p['image'] : 'https://placehold.co/600x400?text=No+Image';
                    $title = htmlspecialchars($p['title']);
                    $category = htmlspecialchars($p['category']);
                    $location = !empty($p['location']) ? htmlspecialchars($p['location']) : '';
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="project-item-clean" onclick="openProjectModal(<?php echo $index; ?>)">
                        <div class="img-wrapper">
                            <img src="<?php echo $coverImg; ?>" alt="<?php echo $title; ?>">
                        </div>
                        <div class="project-info">
                            <h4 class="project-title"><?php echo $title; ?></h4>
                            <div class="project-meta">
                                <span><?php echo $category; ?></span>
                                <?php if($location): ?>
                                    <span class="mx-2">|</span><?php echo $location; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <div class="modal fade" id="projectDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 position-absolute end-0 p-3 z-3">
                    <button type="button" class="btn-close btn-close-white bg-white p-2 rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-0">
                        <div class="col-lg-8 bg-black position-relative">
                            <div id="projectCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                                <div class="carousel-inner" id="carouselInner"></div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#projectCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#projectCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="project-details-container">
                                <h2 class="modal-project-title" id="modalTitle">標題</h2>
                                <div class="modal-project-meta">
                                    <div class="mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i><span id="modalLocation">地點</span></div>
                                    <div class="mb-2"><i class="fas fa-vector-square me-2 text-success"></i><span id="modalArea">坪數</span></div>
                                    <div><i class="fas fa-tag me-2 text-success"></i><span id="modalCategory">分類</span></div>
                                </div>
                                <div class="modal-project-desc" id="modalDesc">
                                    詳細介紹...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) { navbar.classList.add('scrolled'); } else { navbar.classList.remove('scrolled'); }
        });

        // 接收 PHP 傳來的資料
        const globalProjects = <?php echo json_encode($projects); ?>;

        function openProjectModal(index) {
            const data = globalProjects[index];
            if (!data) return;

            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalCategory').textContent = data.category;
            document.getElementById('modalLocation').textContent = data.location || '尚未設定地點';
            document.getElementById('modalArea').textContent = data.area || '尚未設定坪數';
            
            const desc = data.description ? data.description.replace(/\n/g, '<br>') : '暫無詳細介紹';
            document.getElementById('modalDesc').innerHTML = desc;

            const carouselInner = document.getElementById('carouselInner');
            carouselInner.innerHTML = ''; 

            let images = [];
            if (data.gallery) {
                try {
                    images = (typeof data.gallery === 'string') ? JSON.parse(data.gallery) : data.gallery;
                } catch(e) {
                    images = [data.image];
                }
            } else {
                images = [data.image];
            }
            if (!images || images.length === 0) images = [''];

            images.forEach((img, idx) => {
                const activeClass = (idx === 0) ? 'active' : '';
                const imgSrc = img ? `uploads/${img}` : 'https://placehold.co/800x600?text=No+Image';

                const slideHtml = `
                    <div class="carousel-item ${activeClass} h-100">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <img src="${imgSrc}" class="d-block w-100 h-100" style="object-fit: contain;" alt="${data.title}">
                        </div>
                    </div>
                `;
                carouselInner.insertAdjacentHTML('beforeend', slideHtml);
            });

            const myModal = new bootstrap.Modal(document.getElementById('projectDetailModal'));
            myModal.show();
        }
    </script>
</body>
</html>