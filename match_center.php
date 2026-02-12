<?php 
include 'db.php'; 

// التحقق من الآيدي وحماية
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$m = $db->query("SELECT * FROM matches WHERE id = $id")->fetch();

if(!$m) {
    header("Location: index.php");
    exit();
}

// دالة الوقت (اختياري)
$matchTime = strtotime($m['match_time']);
$is_live = ($m['status'] == 'live');
$is_finished = ($m['status'] == 'finished');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مركز المباراة | <?php echo $m['opp_name']; ?></title>
    <link rel="icon" href="icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* --- التصميم الداخلي (Embedded CSS) --- */
        :root {
            --primary: #4b0082;
            --primary-dark: #2a0052;
            --accent: #e0aaff;
            --bg: #050505;
            --card: #141414;
            --text: #fff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Tajawal', sans-serif; }
        body { background: var(--bg); color: var(--text); overflow-x: hidden; }

        /* الهيدر البسيط */
        header {
            padding: 20px; display: flex; justify-content: space-between; align-items: center;
            position: absolute; top: 0; width: 100%; z-index: 10;
        }
        .back-btn { 
            color: white; text-decoration: none; font-size: 1.1rem; font-weight: bold; 
            background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 20px; 
            backdrop-filter: blur(5px); transition: 0.3s;
        }
        .back-btn:hover { background: var(--primary); }

        /* منطقة المباراة (النتيجة) */
        .match-hero {
            min-height: 60vh;
            background: radial-gradient(circle at center, #3d0066 0%, #000 100%);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 80px 20px 40px; position: relative; border-bottom: 4px solid var(--primary);
        }
        
        .league-title { 
            color: var(--accent); font-size: 1rem; margin-bottom: 30px; letter-spacing: 1px; text-transform: uppercase;
            background: rgba(0,0,0,0.3); padding: 5px 15px; border-radius: 10px;
        }

        .score-board {
            display: flex; justify-content: space-around; align-items: center; width: 100%; max-width: 800px;
        }
        
        .team { text-align: center; width: 30%; }
        .team img { 
            height: 100px; width: auto; object-fit: contain; margin-bottom: 15px; 
            filter: drop-shadow(0 0 20px rgba(255,255,255,0.1)); transition: 0.3s;
        }
        .team:hover img { transform: scale(1.1); }
        .team h2 { font-size: 1.4rem; font-weight: 800; margin: 0; }

        .score-area { text-align: center; width: 40%; }
        .score { font-size: 4.5rem; font-weight: 900; line-height: 1; margin-bottom: 10px; }
        .time-box { 
            font-size: 2.5rem; font-weight: 900; background: #fff; color: #000; 
            padding: 10px 20px; border-radius: 10px; display: inline-block; 
        }
        .status { font-size: 1rem; color: #ccc; margin-top: 10px; font-weight: bold; }
        .live-dot { color: red; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        /* تفاصيل المباراة */
        .details-container {
            max-width: 800px; margin: -40px auto 0; padding: 0 20px 40px; position: relative; z-index: 5;
        }
        
        .info-card {
            background: var(--card); border-radius: 20px; padding: 30px; margin-bottom: 20px;
            border: 1px solid #222; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .card-header { 
            border-bottom: 1px solid #333; padding-bottom: 15px; margin-bottom: 15px; 
            font-size: 1.2rem; font-weight: bold; color: var(--accent); display: flex; align-items: center; gap: 10px;
        }

        /* قائمة الهدافين */
        .scorer-item {
            display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #222;
        }
        .scorer-item:last-child { border-bottom: none; }
        .ball-icon { color: gold; }

        /* معلومات الملعب */
        .match-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: center; }
        .meta-box { background: #1a1a1a; padding: 15px; border-radius: 10px; }
        .meta-icon { font-size: 1.5rem; color: var(--primary); margin-bottom: 10px; }
        .meta-label { font-size: 0.8rem; color: #888; }
        .meta-value { font-weight: bold; font-size: 1rem; }

        /* الموبايل */
        @media (max-width: 768px) {
            .team img { height: 70px; }
            .team h2 { font-size: 1rem; }
            .score { font-size: 3rem; }
            .time-box { font-size: 1.8rem; padding: 5px 15px; }
            .match-hero { padding-top: 100px; min-height: 50vh; }
        }
    </style>
</head>
<body>

<header>
    <a href="index.php" class="back-btn"><i class="fas fa-arrow-right"></i> الرئيسية</a>
    <img src="icon.png" height="40" style="opacity:0.8;">
</header>

<div class="match-hero">
    <div class="league-title">دوري الدرجة الاولى</div>
    
    <div class="score-board">
        <div class="team">
            <img src="icon.png" alt="سامراء">
            <h2>اهلي سامراء</h2>
        </div>

        <div class="score-area">
            <?php if($is_live || $is_finished): ?>
                <div class="score"><?php echo $m['home_score']; ?> : <?php echo $m['opp_score']; ?></div>
                <div class="status">
                    <?php if($is_live) echo "<span class='live-dot'>●</span> مباشر"; else echo "نهاية المباراة"; ?>
                </div>
            <?php else: ?>
                <div class="time-box"><?php echo date('H:i', $matchTime); ?></div>
                <div class="status"><?php echo date('d/m/Y', $matchTime); ?></div>
            <?php endif; ?>
        </div>

        <div class="team">
            <img src="uploads/<?php echo $m['opp_logo']; ?>" alt="<?php echo $m['opp_name']; ?>">
            <h2><?php echo $m['opp_name']; ?></h2>
        </div>
    </div>
</div>

<div class="details-container">
    
    <div class="info-card">
        <div class="match-meta">
            <div class="meta-box">
                <i class="fas fa-map-marker-alt meta-icon"></i>
                <div class="meta-label">ملعب المباراة</div>
                <div class="meta-value"><?php echo $m['stadium']; ?></div>
            </div>
            <div class="meta-box">
                <i class="far fa-calendar-alt meta-icon"></i>
                <div class="meta-label">التاريخ</div>
                <div class="meta-value"><?php echo date('Y-m-d', $matchTime); ?></div>
            </div>
        </div>
    </div>

    <?php if($is_finished || $is_live): ?>
    <div class="info-card">
        <div class="card-header"><i class="far fa-futbol"></i> أحداث المباراة</div>
        
        <?php if(!empty($m['scorers'])): ?>
            <div class="scorers-list">
                <?php 
                $scorers = explode(',', $m['scorers']); 
                foreach($scorers as $scorer):
                ?>
                <div class="scorer-item">
                    <i class="fas fa-futbol ball-icon"></i> 
                    <span><?php echo trim($scorer); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:#777; text-align:center;">لا توجد تفاصيل مسجلة للأهداف.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if($is_live): ?>
    <div class="info-card" style="text-align:center;">
        <i class="fas fa-tv" style="font-size:2rem; color:red; margin-bottom:10px;"></i>
        <h3>المباراة جارية الآن</h3>
        <p>تابع البث المباشر عبر القنوات الناقلة</p>
    </div>
    <?php endif; ?>

</div>

</body>
</html>