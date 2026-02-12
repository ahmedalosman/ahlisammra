<?php
include 'db.php';

// التحقق من وجود ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$n = $db->query("SELECT * FROM news WHERE id = $id")->fetch();

// إذا لم يوجد خبر بهذا الرقم
if (!$n) {
    header("Location: index.php");
    exit();
}

// جلب الإعدادات للهيدر
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();

// دالة الوقت
function time_ago($timestamp) {
    $diff = time() - strtotime($timestamp);
    if($diff < 60) return "الآن";
    if($diff < 3600) return "منذ " . floor($diff/60) . " دقيقة";
    if($diff < 86400) return "منذ " . floor($diff/3600) . " ساعة";
    return "منذ " . floor($diff/86400) . " أيام";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $n['title']; ?> | أهلي سامراء</title>
    <link rel="icon" href="icon.png">
    <link rel="stylesheet" href="style.css?v=18.0"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- ستايلات خاصة بصفحة الخبر فقط --- */
        
        /* 1. تأثير الصورة السينمائي */
        .news-hero-section {
            position: relative;
            height: 60vh; /* ارتفاع ممتاز للموبايل */
            margin-top: 75px; /* بعد الهيدر */
            overflow: hidden;
        }
        .news-bg {
            width: 100%; height: 100%;
            object-fit: cover;
            /* تأثير البارالاكس البسيط */
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        /* إذا لم يدعم المتصفح background-image للصورة الديناميكية نستخدم img */
        .news-hero-img {
            width: 100%; height: 100%; object-fit: cover;
            mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
        }

        .news-header-content {
            position: absolute; bottom: 0; left: 0; width: 100%;
            padding: 40px 5%;
            background: linear-gradient(to top, var(--bg-body) 10%, rgba(0,0,0,0.8) 50%, transparent 100%);
            z-index: 2;
        }

        .news-badges { margin-bottom: 15px; display: flex; gap: 10px; align-items: center; }
        .category-tag { background: var(--primary); color: white; padding: 5px 12px; border-radius: 4px; font-weight: bold; font-size: 0.85rem; }
        .date-tag { color: #ccc; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }

        .big-title {
            font-size: clamp(1.8rem, 5vw, 3rem); /* خط متجاوب */
            font-weight: 900; line-height: 1.3; margin-bottom: 10px;
            color: #fff; text-shadow: 0 2px 10px black;
        }

        /* 2. جسم المقال */
        .article-container {
            max-width: 900px; margin: 0 auto; padding: 30px 5%;
            position: relative; z-index: 5;
        }
        
        .article-text {
            font-size: 1.2rem; line-height: 1.8; color: #e0e0e0;
            white-space: pre-line; /* يحترم المسافات والأسطر */
        }
        
        .article-text p { margin-bottom: 20px; }

        /* شريط المشاركة */
        .share-bar {
            display: flex; align-items: center; gap: 15px; margin: 30px 0; padding: 20px 0;
            border-top: 1px solid #333; border-bottom: 1px solid #333;
        }
        .share-txt { font-weight: bold; color: var(--accent); }
        .share-icon { 
            width: 40px; height: 40px; border-radius: 50%; background: #222; color: #fff;
            display: flex; align-items: center; justify-content: center; transition: 0.3s;
        }
        .share-icon:hover { background: var(--primary); transform: translateY(-3px); }

        /* 3. اقرأ أيضاً */
        .related-section { background: #0f0f0f; padding: 40px 0; border-top: 1px solid #222; }
        
        /* زر العودة */
        .back-btn {
            position: fixed; bottom: 30px; left: 30px; 
            width: 50px; height: 50px; background: var(--primary); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 5px 20px rgba(75, 0, 130, 0.5); z-index: 1000;
            font-size: 1.2rem; transition: 0.3s;
        }
        .back-btn:hover { transform: scale(1.1); background: var(--accent); color: black; }

        @media (max-width: 768px) {
            .news-hero-section { height: 50vh; }
            .article-text { font-size: 1.1rem; }
        }
    </style>
</head>
<body>

<header>
    <?php if($settings['ticker_active']): ?>
    <div class="news-ticker">
        <div class="ticker-wrap">
            <div class="ticker-item"><i class="fas fa-bullhorn"></i> <?php echo $settings['ticker_text']; ?></div>
            <div class="ticker-item"><i class="fas fa-bullhorn"></i> <?php echo $settings['ticker_text']; ?></div>
        </div>
    </div>
    <?php endif; ?>
    <div class="header-main">
        <div class="logo"><a href="index.php"><img src="icon.png" alt="Logo"></a></div>
        <div class="hamburger" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
    </div>
</header>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>
<div class="side-menu" id="sideMenu">
    <div style="text-align:left"><i class="fas fa-times" onclick="toggleMenu()" style="font-size:2rem; color:white; cursor:pointer"></i></div>
    <div style="text-align:center; padding:30px;"><img src="icon.png" width="100"></div>
    <div class="menu-links">
        <a href="index.php">الرئيسية</a>
        <a href="all_news.php">الأخبار</a>
        <a href="index.php#matches">المباريات</a>
        <a href="index.php#store">المتجر</a>
        <a href="members.php">الإدارة</a>
    </div>
</div>

<section class="news-hero-section">
    <img src="uploads/<?php echo $n['img']; ?>" class="news-hero-img" alt="<?php echo $n['title']; ?>">
    
    <div class="news-header-content">
        <div class="news-badges">
            <span class="category-tag"><?php echo $n['cat']; ?></span>
            <span class="date-tag"><i class="far fa-clock"></i> <?php echo time_ago($n['created_at']); ?></span>
        </div>
        <h1 class="big-title"><?php echo $n['title']; ?></h1>
    </div>
</section>

<article class="article-container">
    <div class="article-text">
        <?php echo nl2br($n['content']); ?>
    </div>

    <div class="share-bar">
        <span class="share-txt">مشاركة الخبر:</span>
        <a href="#" class="share-icon"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="share-icon"><i class="fab fa-twitter"></i></a>
        <a href="#" class="share-icon"><i class="fab fa-whatsapp"></i></a>
        <a href="#" class="share-icon" onclick="copyLink()"><i class="fas fa-link"></i></a>
    </div>
</article>

<section class="related-section">
    <div class="section-head" style="padding-top:0;">
        <div class="title" style="font-size:1.5rem;">أخبار قد تهمك</div>
    </div>
    
    <div class="swipe-container">
        <?php
        // جلب 4 أخبار غير الخبر الحالي
        $related = $db->query("SELECT * FROM news WHERE id != $id ORDER BY id DESC LIMIT 4")->fetchAll();
        foreach($related as $r):
        ?>
        <div class="swipe-item news-card" onclick="location.href='news_details.php?id=<?php echo $r['id']; ?>'" style="cursor:pointer;">
            <div class="news-img-box" style="height:160px;"> <img src="uploads/<?php echo $r['img']; ?>">
                <div class="news-overlay"><span class="news-cat"><?php echo $r['cat']; ?></span></div>
            </div>
            <div class="news-body" style="padding:15px;">
                <h3 class="news-title" style="font-size:1rem; margin-bottom:5px;"><?php echo $r['title']; ?></h3>
                <div class="time-ago" style="font-size:0.7rem;">
                    <i class="far fa-clock"></i> <?php echo time_ago($r['created_at']); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<a href="index.php" class="back-btn"><i class="fas fa-arrow-right"></i></a>

<footer>
    <div class="footer-content" style="text-align:center;">
        <img src="kt.png" class="kt-logo-footer" style="width:150px;">
        <p class="copyright" style="margin-top:10px;">&copy; 2025 نادي أهلي سامراء</p>
    </div>
</footer>

<script>
    function toggleMenu() {
        document.getElementById('sideMenu').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    }
    
    function copyLink() {
        navigator.clipboard.writeText(window.location.href);
        alert("تم نسخ رابط الخبر!");
    }
</script>

</body>
</html>