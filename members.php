<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مجلس الإدارة | أهلي سامراء</title>
    <link rel="icon" href="icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* --- إعدادات عامة --- */
        :root {
            --primary: #4b0082;       /* بنفسجي */
            --primary-dark: #240046;  
            --accent: #e0aaff;        /* ليلكي */
            --bg-body: #050505;       /* أسود */
            --card-bg: #141414;       /* كروت */
            --text: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Tajawal', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: var(--bg-body); color: var(--text); direction: rtl; overflow-x: hidden; line-height: 1.6; }
        a { text-decoration: none; color: inherit; transition: 0.3s; }

        /* --- الهيدر --- */
        header {
            background: rgba(0, 0, 0, 0.9); backdrop-filter: blur(10px);
            height: 70px; display: flex; justify-content: space-between; align-items: center;
            padding: 0 5%; position: fixed; top: 0; width: 100%; z-index: 1000;
            border-bottom: 2px solid var(--primary);
        }
        .logo img { height: 50px; }
        .back-btn { 
            color: white; font-weight: bold; font-size: 1.1rem; 
            display: flex; align-items: center; gap: 5px; 
            background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 20px;
        }

        /* --- 1. قسم الرئيس (Hero Section) --- */
        .president-hero {
            margin-top: 70px;
            /* خلفية متدرجة ملكية */
            background: radial-gradient(circle at top left, #2e004d 0%, #000000 80%);
            min-height: 550px;
            display: flex; align-items: flex-end; justify-content: center;
            padding: 0 5%; position: relative; overflow: hidden;
            border-bottom: 5px solid var(--primary);
        }

        /* تأثيرات إضافية للخلفية */
        .president-hero::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url('pattern.png'); opacity: 0.05; /* نمط خفيف إذا وجد */
        }

        .president-container {
            display: flex; width: 100%; max-width: 1200px;
            justify-content: space-between; align-items: center;
            flex-wrap: wrap-reverse; /* عكس الترتيب في الموبايل */
            z-index: 2;
        }

        .pres-info {
            flex: 1; padding: 50px 0;
            animation: fadeInRight 1s ease-out;
        }
        
        .pres-role {
            color: var(--accent); font-size: 1.5rem; font-weight: 800; letter-spacing: 1px;
            display: inline-block; margin-bottom: 10px; text-transform: uppercase;
            border-bottom: 3px solid var(--primary); padding-bottom: 5px;
        }
        
        .pres-name {
            color: white; font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 900; line-height: 1.1;
            text-shadow: 0 10px 30px rgba(0,0,0,0.9); margin-bottom: 20px;
        }
        
        .pres-quote {
            color: #ccc; font-size: 1.1rem; border-right: 3px solid var(--accent);
            padding-right: 15px; max-width: 500px;
        }

        /* حاوية صورة الرئيس */
        .pres-image {
            flex: 1; display: flex; justify-content: flex-end; position: relative; 
            height: 550px; /* ارتفاع ثابت للصورة */
            animation: fadeInUp 1s ease-out;
        }

        /* --- تنسيق صورة الرئيس (الفلتر والشفافية) --- */
        .pres-image img {
            height: 100%; width: auto; object-fit: contain;
            /* 1. الفلتر الغامق (Dark Filter) */
            filter: drop-shadow(0 0 20px rgba(0,0,0,0.8)) brightness(0.85) contrast(1.1);
            
            /* 2. الشفافية من الأسفل (Fade from bottom) */
            -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
            mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
            
            transition: 0.5s;
        }
        
        /* تأثير عند التحويم */
        .pres-image:hover img {
            filter: drop-shadow(0 0 30px rgba(75, 0, 130, 0.6)) brightness(1); /* يضيء عند التحويم */
            transform: scale(1.02);
        }

        /* --- 2. شبكة الأعضاء --- */
        .board-section { padding: 60px 5%; background: #0a0a0a; }
        
        .section-title { 
            font-size: 2rem; font-weight: 900; color: white; margin-bottom: 40px; 
            text-align: center; position: relative;
        }
        .section-title::after {
            content: ''; display: block; width: 50px; height: 4px; background: var(--primary);
            margin: 10px auto; border-radius: 2px;
        }

        .board-grid {
            display: grid; 
            /* التجاوب الذكي: عمودين في الموبايل، 4 في الكمبيوتر */
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 25px;
        }

        .board-card {
            background: var(--card-bg); border-radius: 15px; overflow: hidden;
            border: 1px solid #222; transition: 0.3s; position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .board-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 10px 25px rgba(75, 0, 130, 0.3); }

        .card-img {
            height: 260px; width: 100%;
            background: linear-gradient(to top, #111, #222);
            position: relative; overflow: hidden;
        }
        .card-img img { 
            width: 100%; height: 100%; object-fit: cover; object-position: top center; 
            transition: 0.5s;
        }
        .board-card:hover .card-img img { transform: scale(1.1); }

        .card-info { 
            padding: 15px; text-align: center; background: #151515; 
            border-top: 3px solid var(--primary); z-index: 2; position: relative;
        }
        .card-info h3 { font-size: 1.1rem; color: white; margin-bottom: 5px; font-weight: 800; }
        .card-info p { color: var(--accent); font-size: 0.85rem; font-weight: bold; }

        /* --- الفوتر --- */
        footer { 
            background: #000; padding: 40px 20px; text-align: center; 
            border-top: 4px solid var(--primary); 
        }
        .kt-logo { width: 180px; filter: drop-shadow(0 0 10px var(--primary)); opacity: 0.9; margin-bottom: 15px; }

        /* --- تحسينات الموبايل (Responsive Fixes) --- */
        @media (max-width: 768px) {
            /* تعديل قسم الرئيس للموبايل */
            .president-hero { min-height: auto; padding-top: 30px; padding-bottom: 0; }
            .president-container { flex-direction: column-reverse; text-align: center; }
            
            .pres-image { 
                height: 350px; width: 100%; 
                justify-content: center; align-items: flex-end; /* تثبيت الصورة في الأسفل */
            }
            .pres-image img { height: 100%; max-width: 100%; }
            
            .pres-info { padding: 20px 0 40px; }
            .pres-name { font-size: 2.5rem; margin-bottom: 10px; }
            .pres-role { font-size: 1.2rem; display: inline-block; margin-bottom: 15px; }
            .pres-quote { margin: 0 auto; font-size: 0.9rem; border-right: none; border-top: 2px solid var(--accent); padding-top: 10px; }

            /* تعديل شبكة الأعضاء للموبايل */
            .board-grid {
                grid-template-columns: repeat(2, 1fr); /* جعلها عمودين في الموبايل لتبدو أجمل */
                gap: 15px;
            }
            .card-img { height: 200px; } /* تصغير ارتفاع الصور قليلاً */
            .card-info h3 { font-size: 1rem; }
            .card-info p { font-size: 0.8rem; }
        }

        /* حركات بسيطة */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    </style>
</head>
<body>

<header>
    <div class="logo"><a href="index.php"><img src="icon.png" alt="Ahli Samarra"></a></div>
    <a href="index.php" class="back-btn"><i class="fas fa-arrow-right"></i> عودة للرئيسية</a>
</header>

<?php
// جلب الأعضاء
$all_members = $db->query("SELECT * FROM members")->fetchAll();
$pres = null;
$board = [];

// فصل الرئيس عن البقية
foreach($all_members as $m) {
    if (strpos($m['role'], 'رئيس') !== false) {
        $pres = $m;
    } else {
        $board[] = $m;
    }
}
?>

<?php if($pres): ?>
<section class="president-hero">
    <div class="president-container">
        <div class="pres-info">
            <span class="pres-role"><?php echo $pres['role']; ?></span>
            <h1 class="pres-name"><?php echo $pres['name']; ?></h1>
            <div class="pres-quote">
                "نعمل ليل نهار لرفع اسم النادي عالياً، بدعمكم ومساندتكم سنحقق المستحيل."
            </div>
        </div>
        <div class="pres-image">
            <img src="uploads/<?php echo $pres['img']; ?>" alt="President">
        </div>
    </div>
</section>
<?php endif; ?>

<section class="board-section">
    <div class="section-title">أعضاء مجلس الإدارة</div>
    
    <div class="board-grid">
        <?php if(empty($board)): ?>
            <p style="text-align:center; color:#777; width:100%; grid-column: 1/-1;">لا يوجد أعضاء مضافين حالياً.</p>
        <?php else: ?>
            <?php foreach($board as $mem): ?>
            <div class="board-card">
                <div class="card-img">
                    <img src="uploads/<?php echo $mem['img']; ?>" alt="<?php echo $mem['name']; ?>">
                </div>
                <div class="card-info">
                    <h3><?php echo $mem['name']; ?></h3>
                    <p><?php echo $mem['role']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<footer>
    <img src="kt.png" class="kt-logo" alt="Ahli Samarra">
    <p style="color:#666; font-size:0.9rem; margin-top:10px;">جميع الحقوق محفوظة لنادي أهلي سامراء © 2025</p>
</footer>

</body>
</html>