<?php
// إعدادات الاتصال
$db_file = "club_v7.db";

try {
    // إنشاء الملف والاتصال به
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==========================================
    // 1. جدول الإعدادات (Settings)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY, 
        site_name TEXT DEFAULT 'أهلي سامراء',
        site_status INTEGER DEFAULT 1, -- 1=مفتوح 0=مغلق
        close_msg TEXT DEFAULT 'الموقع مغلق للصيانة',
        ticker_active INTEGER DEFAULT 1,
        ticker_text TEXT DEFAULT 'أهلاً بكم في الموقع الرسمي لنادي أهلي سامراء'
    )");

    // إدخال الإعدادات الافتراضية إذا كانت فارغة
    $check = $db->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($check == 0) {
        $db->exec("INSERT INTO settings (site_status) VALUES (1)");
    }

    // ==========================================
    // 2. جدول المستخدمين (Users)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY, 
        username TEXT, 
        password TEXT, 
        role TEXT, -- admin, media, designer
        full_name TEXT,
        avatar TEXT DEFAULT 'default_user.png'
    )");

    // إعادة حساب المدير الافتراضي
    $check = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($check == 0) {
        // admin / 123456
        $db->exec("INSERT INTO users (username, password, role, full_name) VALUES ('admin', '123456', 'admin', 'المدير العام')");
        $db->exec("INSERT INTO users (username, password, role, full_name) VALUES ('media', '123456', 'media', 'الإعلامي')");
        $db->exec("INSERT INTO users (username, password, role, full_name) VALUES ('design', '123456', 'designer', 'المصمم')");
    }

    // ==========================================
    // 3. جدول الأخبار (News)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS news (
        id INTEGER PRIMARY KEY, 
        title TEXT, 
        content TEXT, 
        img TEXT, 
        cat TEXT, 
        is_hero INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // ==========================================
    // 4. جدول المباريات (Matches)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS matches (
        id INTEGER PRIMARY KEY, 
        opp_name TEXT, 
        opp_logo TEXT, 
        match_time DATETIME, 
        stadium TEXT, 
        status TEXT, -- upcoming, live, finished
        home_score INTEGER DEFAULT 0,
        opp_score INTEGER DEFAULT 0,
        scorers TEXT
    )");

    // ==========================================
    // 5. جدول اللاعبين (Players)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS players (
        id INTEGER PRIMARY KEY, 
        name TEXT, 
        number INTEGER, 
        role TEXT, 
        img TEXT
    )");

    // ==========================================
    // 6. جدول مجلس الإدارة (Members)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS members (
        id INTEGER PRIMARY KEY, 
        name TEXT, 
        role TEXT, 
        img TEXT
    )");

    // ==========================================
    // 7. جدول المتجر (Store)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS store (
        id INTEGER PRIMARY KEY, 
        name TEXT, 
        price TEXT, 
        img TEXT
    )");

    // ==========================================
    // 8. جدول الأكاديمية (Academy)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS academy (
        id INTEGER PRIMARY KEY, 
        title TEXT, 
        content TEXT, 
        img TEXT
    )");

    // ==========================================
    // 9. جدول الإشعارات الإدارية (Notifications)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY, 
        sender_id INTEGER, 
        target_role TEXT, 
        message TEXT, 
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // ==========================================
    // 10. جدول طلبات التصميم (Design Requests)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS design_requests (
        id INTEGER PRIMARY KEY, 
        requester_id INTEGER, 
        title TEXT, 
        details TEXT, 
        status TEXT DEFAULT 'pending', 
        design_img TEXT, 
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // ==========================================
    // 11. جدول سجل المراقبة (Activity Logs)
    // ==========================================
    $db->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INTEGER PRIMARY KEY, 
        user_id INTEGER, 
        username TEXT, 
        role TEXT,
        action TEXT, 
        details TEXT, 
        ip_address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>استعادة النظام</title>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@700&display=swap" rel="stylesheet">
        <style>
            body { background: #121212; color: #00ff41; font-family: 'Tajawal'; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; }
            .box { border: 2px solid #00ff41; padding: 40px; border-radius: 10px; background: #000; box-shadow: 0 0 20px #00ff41; }
            h1 { font-size: 3rem; margin: 0; }
            p { color: #fff; font-size: 1.2rem; }
            .btn { background: #00ff41; color: black; padding: 10px 20px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 20px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>✅ تم بنجاح!</h1>
            <p>تم استعادة هيكل قاعدة البيانات (11 جدول) بالكامل.</p>
            <p>تم إنشاء حساب المدير: <b>admin</b> / الرمز: <b>123456</b></p>
            <a href="login.php" class="btn">الذهاب لتسجيل الدخول</a>
        </div>
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    echo "<h1 style='color:red'>خطأ فادح: " . $e->getMessage() . "</h1>";
}
?>