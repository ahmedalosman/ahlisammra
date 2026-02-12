<?php 
include 'db.php'; 
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();

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
    <title>أهلي سامراء</title>
    <link rel="icon" href="icon.png">
    <link rel="stylesheet" href="style.css?v=16.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <a href="#matches">المباريات</a>
        <a href="#store">المتجر</a>
        <a href="members.php">الإدارة</a>
    </div>
</div>

<section class="hero-slider">
    <?php
    $heroes = $db->query("SELECT * FROM news WHERE is_hero = 1 ORDER BY id DESC LIMIT 5")->fetchAll();
    foreach($heroes as $i => $h):
    ?>
    <div class="slide <?php echo $i===0?'active':''; ?>">
        <img src="uploads/<?php echo $h['img']; ?>">
        <div class="slide-caption">
            <span class="cat-badge"><?php echo $h['cat']; ?></span>
            <h1><?php echo $h['title']; ?></h1>
            <a href="news_details.php?id=<?php echo $h['id']; ?>" class="btn-slide">اقرأ التفاصيل <i class="fas fa-arrow-left"></i></a>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<div class="section-head">
    <div class="title">المباريات</div>
    <a href="#" class="view-all-btn">الجدول</a>
</div>
<div class="swipe-container" id="matches">
    <?php
    $matches = $db->query("SELECT * FROM matches ORDER BY match_time DESC LIMIT 4")->fetchAll();
    foreach($matches as $m):
        $is_live = ($m['status'] == 'live');
    ?>
    <div class="swipe-item match-card">
        <div class="status-badge <?php echo $is_live?'live':''; ?>">
            <?php echo $is_live ? '● مباشر' : date('d M | H:i', strtotime($m['match_time'])); ?>
        </div>
        <div class="teams-row">
            <div class="team-col"><img src="icon.png"><span>اهلي سامراء</span></div>
            <div class="vs-col"><?php echo ($m['status']!='upcoming') ? $m['home_score'].'-'.$m['opp_score'] : 'VS'; ?></div>
            <div class="team-col"><img src="uploads/<?php echo $m['opp_logo']; ?>"><span><?php echo $m['opp_name']; ?></span></div>
        </div>
        <a href="match_center.php?id=<?php echo $m['id']; ?>" class="match-btn">مركز المباراة</a>
    </div>
    <?php endforeach; ?>
</div>

<div class="section-head">
    <div class="title">أحدث الأخبار</div>
    <a href="all_news.php" class="view-all-btn">كل الأخبار</a>
</div>
<div class="swipe-container">
    <?php
    $news = $db->query("SELECT * FROM news ORDER BY id DESC LIMIT 6")->fetchAll();
    foreach($news as $n):
    ?>
    <div class="swipe-item news-card" onclick="location.href='news_details.php?id=<?php echo $n['id']; ?>'" style="cursor:pointer;">
        <div class="news-img-box">
            <img src="uploads/<?php echo $n['img']; ?>">
            <div class="news-overlay"><span class="news-cat"><?php echo $n['cat']; ?></span></div>
        </div>
        <div class="news-body">
            <h3 class="news-title"><?php echo $n['title']; ?></h3>
            <div class="news-meta">
                <span><i class="far fa-clock"></i> <?php echo time_ago($n['created_at']); ?></span>
                <span>المزيد <i class="fas fa-chevron-left"></i></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="section-head"><div class="title">المتجر</div></div>
<div class="swipe-container" id="store">
    <?php
    $store = $db->query("SELECT * FROM store LIMIT 4")->fetchAll();
    foreach($store as $item):
    ?>
    <div class="swipe-item store-card">
        <img src="uploads/<?php echo $item['img']; ?>">
        <span class="price-tag"><?php echo $item['price']; ?> د.ع</span>
        <h4 style="margin-bottom:15px;"><?php echo $item['name']; ?></h4>
        <button class="buy-btn">أضف للسلة</button>
    </div>
    <?php endforeach; ?>
</div>

<div class="section-head"><div class="title">تصفح أكثر</div></div>
<div class="browse-section">
    <div class="browse-card" onclick="location.href='academy.php'">
        <img src="uploads/1.png" alt="Academy"> 
        <div class="browse-overlay">
            <div class="browse-title">الأكاديمية</div>
            <div class="browse-subtitle">جيل المستقبل</div>
        </div>
    </div>
    <div class="browse-card" onclick="location.href='history.php'">
        <img src="uploads/2.png" alt="History">
        <div class="browse-overlay">
            <div class="browse-title">تاريخ النادي</div>
            <div class="browse-subtitle">مسيرة وبطولات</div>
        </div>
    </div>
    <div class="browse-card" onclick="location.href='members.php'">
        <img src="uploads/3.png" alt="Board">
        <div class="browse-overlay">
            <div class="browse-title">الإدارة</div>
            <div class="browse-subtitle">الهيكل الإداري</div>
        </div>
    </div>
</div>

<div class="section-head"><div class="title">اللاعبين</div></div>
<div class="players-wrapper">
    <?php
    $players = $db->query("SELECT * FROM players LIMIT 10")->fetchAll();
    foreach($players as $p):
    ?>
    <div class="player-card-pro">
        <div class="player-bg-num"><?php echo $p['number']; ?></div>
        <img src="uploads/<?php echo $p['img']; ?>" class="player-img">
        <div class="player-info">
            <div class="p-name"><?php echo $p['name']; ?></div>
            <div class="p-role"><?php echo $p['role']; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<footer>
    <img src="kt.png" class="kt-logo footer-logo" alt="Ahli Samarra">
    <p class="footer-bio">
        نادي أهلي سامراء، , رمز للرياضة العراقية الأصيلة.
    </p>
    
    <div class="footer-socials">
        <a href="https://www.facebook.com/profile.php?id=100057642121947"><i class="fab fa-facebook-f"></i></a>
    </div>

    <div class="copy">جميع الحقوق محفوظة لنادي أهلي سامراء &copy; 2026</div>
</footer>

<script>
    function toggleMenu() {
        document.getElementById('sideMenu').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    }
    let current = 0;
    const slides = document.querySelectorAll('.slide');
    if(slides.length > 0) {
        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 5000);
    }
</script>
</body>
</html>