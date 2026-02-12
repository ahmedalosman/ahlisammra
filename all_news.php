<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كل الأخبار | أهلي سامراء</title>
    <link rel="icon" href="icon.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header>
    <div class="header-main">
        <div class="logo"><a href="index.php"><img src="icon.png"></a></div>
        <a href="index.php" style="color:white; font-size:1.2rem;"><i class="fas fa-arrow-left"></i> عودة</a>
    </div>
</header>

<div class="section-title" style="margin-top:100px;"><span>أرشيف الأخبار</span></div>

<div class="news-grid-page">
    <?php
    $all_news = $db->query("SELECT * FROM news ORDER BY id DESC")->fetchAll();
    foreach($all_news as $n):
    ?>
    <div class="news-card">
        <img src="uploads/<?php echo $n['img']; ?>">
        <div class="news-body">
            <small style="color:var(--accent);"><?php echo date('Y-m-d', strtotime($n['created_at'])); ?></small>
            <h3><?php echo $n['title']; ?></h3>
            <a href="news_view.php?id=<?php echo $n['id']; ?>" style="color:var(--primary); font-weight:bold;">اقرأ المزيد</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

</body>
</html>