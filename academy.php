<?php 
include 'db.php'; 
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكاديمية أهلي سامراء</title>
    <link rel="icon" href="icon.png">
    <link rel="stylesheet" href="style.css?v=20.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- ستايلات خاصة بالأكاديمية --- */
        .acad-hero {
            height: 70vh; position: relative; margin-top: 75px; overflow: hidden;
            background: url('uploads/academy_bg.jpg') center/cover fixed no-repeat; /* صورة خلفية ثابتة */
        }
        .acad-overlay {
            position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3), #050505);
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
        }
        .acad-title { font-size: 3.5rem; font-weight: 900; color: #fff; text-shadow: 0 5px 20px black; margin-bottom: 15px; }
        .acad-subtitle { font-size: 1.2rem; color: var(--accent); max-width: 600px; line-height: 1.6; }

        .programs-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px; padding: 50px 5%;
        }
        .prog-card {
            background: #111; border: 1px solid #222; border-radius: 20px; overflow: hidden;
            transition: 0.4s; position: relative;
        }
        .prog-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 10px 30px rgba(75, 0, 130, 0.2); }
        .prog-img { height: 250px; width: 100%; object-fit: cover; transition: 0.5s; }
        .prog-card:hover .prog-img { transform: scale(1.1); }
        .prog-content { padding: 30px; position: relative; background: #111; }
        .prog-icon { 
            position: absolute; top: -25px; left: 25px; width: 50px; height: 50px; 
            background: var(--primary); color: white; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
            box-shadow: 0 5px 10px rgba(0,0,0,0.5);
        }
        .prog-title { font-size: 1.4rem; font-weight: bold; margin-bottom: 10px; color: white; }
        .prog-desc { color: #999; line-height: 1.7; font-size: 0.95rem; }

        .cta-section {
            background: linear-gradient(45deg, var(--primary-dark), #000);
            padding: 60px 5%; text-align: center; margin: 50px 5%; border-radius: 20px;
            border: 1px solid var(--primary);
        }
        .join-btn {
            background: white; color: var(--primary); padding: 12px 40px; 
            border-radius: 30px; font-weight: 900; font-size: 1.1rem; 
            display: inline-block; margin-top: 20px; transition: 0.3s;
        }
        .join-btn:hover { background: var(--accent); color: #000; transform: scale(1.05); }
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
        <a href="academy.php" style="color:var(--accent);">الأكاديمية</a>
        <a href="all_news.php">الأخبار</a>
        <a href="index.php#matches">المباريات</a>
        <a href="members.php">الإدارة</a>
    </div>
</div>

<section class="acad-hero">
    <div class="acad-overlay">
        <h1 class="acad-title">أكاديمية المستقبل</h1>
        <p class="acad-subtitle">حيث نصنع نجوم الغد.. بيئة احترافية، مدربين مختصين، ومنهجية عالمية لتطوير المواهب في سامراء.</p>
    </div>
</section>

<div class="section-head" style="margin-top:20px;">
    <div class="title">برامجنا التدريبية</div>
</div>

<div class="programs-grid">
    <?php
    // جلب البيانات من جدول الأكاديمية
    $progs = $db->query("SELECT * FROM academy ORDER BY id ASC")->fetchAll();
    foreach($progs as $p):
    ?>
    <div class="prog-card">
        <div style="overflow:hidden;">
            <img src="uploads/<?php echo $p['img']; ?>" class="prog-img" alt="Academy">
        </div>
        <div class="prog-content">
            <div class="prog-icon"><i class="fas fa-running"></i></div>
            <h3 class="prog-title"><?php echo $p['title']; ?></h3>
            <p class="prog-desc"><?php echo $p['content']; ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="cta-section">
    <h2 style="color:white; font-size:2rem; margin-bottom:10px;">هل تريد الانضمام لاكاديمية الاهلي؟</h2>
    <p style="color:#ccc;">التسجيل مفتوح الآن للفئات العمرية من 2010 إلى 2015</p>
    <a href="#" class="join-btn">سجل الآن</a>
</div>

<footer>
    <img src="kt.png" class="kt-logo-footer" style="width:200px;">
    <p style="color:#888; margin-top:15px;">&copy; 2025 أكاديمية أهلي سامراء الرياضية</p>
</footer>

<script>
    function toggleMenu() {
        document.getElementById('sideMenu').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    }
</script>

</body>
</html>