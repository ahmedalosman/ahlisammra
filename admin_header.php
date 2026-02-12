<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// 1. الحماية: التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 2. جلب بيانات المستخدم الحالية
$page = basename($_SERVER['PHP_SELF']);
$my_id = $_SESSION['admin_id'];
$my_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin'; // admin, media, designer
$my_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'المدير';

// 3. نظام الإشعارات الذكي
$notif_count = 0;
$notif_text = "لا توجد إشعارات جديدة";
$notif_link = "#";

// جلب الإشعارات حسب الدور
if ($my_role == 'designer') {
    // المصمم: يرى عدد الطلبات المعلقة
    $stmt = $db->query("SELECT COUNT(*) FROM design_requests WHERE status = 'pending'");
    $notif_count = $stmt->fetchColumn();
    if ($notif_count > 0) {
        $notif_text = "لديك $notif_count طلبات تصميم جديدة بانتظارك!";
        $notif_link = "admin_requests.php";
    }
} elseif ($my_role == 'media') {
    // الإعلامي: يرى التصاميم المكتملة
    $stmt = $db->query("SELECT COUNT(*) FROM design_requests WHERE status = 'done' AND requester_id = $my_id");
    $notif_count = $stmt->fetchColumn();
    if ($notif_count > 0) {
        $notif_text = "تم إنجاز $notif_count تصميم، يرجى المراجعة!";
        $notif_link = "admin_requests.php";
    }
}

// جلب إشعارات المدير العام (التعاميم) للجميع
if($my_role != 'admin') {
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE target_role = ? OR target_role = 'all'");
    $stmt->execute([$my_role]);
    $admin_alerts = $stmt->fetchColumn();
    if($admin_alerts > 0) {
        $notif_count += $admin_alerts;
        $notif_text = "يوجد تعميم إداري جديد، يرجى الاطلاع.";
        // هنا يمكن توجيههم لصفحة عرض التعاميم لاحقاً
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | أهلي سامراء</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* --- المتغيرات والتأسيس --- */
        :root { 
            --bg-body: #121212; 
            --bg-sidebar: #050505; 
            --bg-card: #1e1e1e;
            --primary: #5a189a; 
            --accent: #e0aaff; 
            --text-main: #ffffff;
            --text-muted: #aaaaaa;
            --border: #333333;
        }

        * { box-sizing: border-box; font-family: 'Tajawal', sans-serif; -webkit-tap-highlight-color: transparent; outline: none; }
        body { margin: 0; background: var(--bg-body); color: var(--text-main); display: flex; min-height: 100vh; overflow-x: hidden; }
        a { text-decoration: none; transition: 0.3s; }
        ul { list-style: none; padding: 0; margin: 0; }

        /* --- القائمة الجانبية (Sidebar) --- */
        .sidebar {
            width: 260px; background: var(--bg-sidebar); border-left: 1px solid var(--border);
            display: flex; flex-direction: column; padding: 20px; position: fixed; height: 100%; top: 0; right: 0; z-index: 1000;
            transition: 0.4s cubic-bezier(0.77, 0, 0.175, 1);
        }
        
        .sidebar-header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }
        .sidebar-header img { width: 70px; margin-bottom: 10px; filter: drop-shadow(0 0 5px var(--primary)); }
        .sidebar-header h3 { margin: 0; color: var(--accent); font-size: 1.1rem; }
        .user-role-badge { font-size: 0.75rem; background: #333; color: #ccc; padding: 2px 8px; border-radius: 4px; margin-top: 5px; display: inline-block; }

        /* فواصل الأقسام في القائمة */
        .menu-label { font-size: 0.75rem; color: #555; margin-top: 15px; margin-bottom: 5px; font-weight: bold; padding-right: 10px; }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 15px; color: var(--text-muted); text-decoration: none;
            margin-bottom: 5px; border-radius: 10px; font-weight: 500; font-size: 0.95rem;
        }
        .nav-link:hover, .nav-link.active { background: var(--primary); color: white; transform: translateX(-5px); }
        .nav-link i { width: 25px; text-align: center; font-size: 1.1rem; }

        /* --- المحتوى الرئيسي (Main Content) --- */
        .main-content { margin-right: 260px; padding: 30px; width: 100%; transition: 0.4s; }

        /* --- الشريط العلوي (Top Header) --- */
        .top-header {
            display: flex; justify-content: space-between; align-items: center;
            background: var(--bg-card); padding: 15px 25px; border-radius: 15px;
            border: 1px solid var(--border); margin-bottom: 30px; position: relative;
        }
        
        .menu-toggle { display: none; font-size: 1.5rem; color: white; cursor: pointer; margin-left: 15px; }
        .header-title h4 { margin: 0; color: var(--accent); font-size: 1.1rem; }

        /* --- الإشعارات (Notification) --- */
        .notif-wrapper { position: relative; }
        .notification-box {
            position: relative; cursor: pointer; width: 45px; height: 45px; background: #2a2a2a; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; transition: 0.3s;
        }
        .notification-box:hover { background: var(--primary); color: white; }
        .notification-box i { font-size: 1.2rem; }
        .badge {
            position: absolute; top: -2px; right: -2px; background: #ff4d4d; color: white;
            font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 50%; border: 2px solid var(--bg-card);
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }

        /* القائمة المنسدلة للإشعارات */
        .notif-dropdown {
            position: absolute; top: 60px; left: 0; width: 280px;
            background: #222; border: 1px solid #444; border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); opacity: 0; visibility: hidden;
            transform: translateY(-10px); transition: 0.3s; z-index: 2000;
        }
        .notif-dropdown.active { opacity: 1; visibility: visible; transform: translateY(0); }
        .notif-header { padding: 15px; border-bottom: 1px solid #333; font-weight: bold; color: white; background: #2a2a2a; border-radius: 12px 12px 0 0; display:flex; justify-content:space-between; }
        .notif-body { padding: 15px; font-size: 0.9rem; color: #ccc; }
        .notif-empty { text-align: center; padding: 20px; color: #777; }
        .notif-link { display: block; margin-top: 10px; color: var(--accent); font-size: 0.85rem; text-decoration: underline; }

        /* --- عناصر UI عامة --- */
        .card { background: var(--bg-card); padding: 25px; border-radius: 15px; border: 1px solid var(--border); margin-bottom: 30px; overflow: hidden; }
        h2 { border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 20px; display: inline-block; color: white; }
        
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #ddd; }
        input, select, textarea { 
            width: 100%; padding: 12px; margin-bottom: 20px; background: #2a2a2a; 
            border: 1px solid #444; color: white; border-radius: 8px; font-size: 1rem;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); }
        
        button.btn-save { 
            background: var(--primary); color: white; border: none; padding: 14px; 
            border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; font-size: 1rem; transition: 0.3s;
        }
        button.btn-save:hover { background: #420075; }
        
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: #252525; color: var(--accent); padding: 15px; text-align: right; }
        td { padding: 15px; border-bottom: 1px solid #333; color: #ddd; }
        .btn-del { color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 5px 10px; border-radius: 5px; font-size: 0.85rem; }

        .overlay-admin { position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 900; display: none; }
        .overlay-admin.active { display: block; }

        @media (max-width: 768px) {
            .sidebar { right: -260px; } .sidebar.active { right: 0; }
            .main-content { margin-right: 0; padding: 15px; }
            .top-header { padding: 10px 15px; }
            .menu-toggle { display: block; }
            .header-title h4 { font-size: 0.9rem; }
            .notif-dropdown { width: 280px; left: -20px; }
        }
    </style>
</head>
<body>

<div class="overlay-admin" id="overlayAdmin" onclick="toggleSidebar()"></div>

<div class="sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <img src="icon.png" alt="Logo">
        <h3>نظام الإدارة</h3>
        <span class="user-role-badge">
            <?php 
                if($my_role == 'admin') echo 'المدير العام';
                elseif($my_role == 'media') echo 'المكتب الإعلامي';
                elseif($my_role == 'designer') echo 'فريق التصميم';
            ?>
        </span>
    </div>
    
    <div style="overflow-y: auto; flex:1;">
        <div class="menu-label">الرئيسية</div>
        <a href="admin.php" class="nav-link <?php if($page=='admin.php') echo 'active'; ?>">
            <i class="fas fa-home"></i> <span>لوحة القيادة</span>
        </a>

        <div class="menu-label">العمليات</div>
        <?php if($my_role == 'admin'): ?>
        <a href="admin_send_notif.php" class="nav-link <?php if($page=='admin_send_notif.php') echo 'active'; ?>">
            <i class="fas fa-bullhorn"></i> <span>إرسال تعميم</span>
        </a>
        <?php endif; ?>
        
        <a href="admin_requests.php" class="nav-link <?php if($page=='admin_requests.php') echo 'active'; ?>">
            <i class="fas fa-paint-brush"></i> <span>طلبات التصميم</span>
        </a>

        <?php if($my_role == 'admin' || $my_role == 'media'): ?>
            <div class="menu-label">المحتوى</div>
            <a href="admin_news.php" class="nav-link <?php if($page=='admin_news.php') echo 'active'; ?>">
                <i class="fas fa-newspaper"></i> <span>الأخبار والمقالات</span>
            </a>
            <a href="admin_matches.php" class="nav-link <?php if($page=='admin_matches.php') echo 'active'; ?>">
                <i class="fas fa-futbol"></i> <span>جدول المباريات</span>
            </a>
            <a href="admin_academy.php" class="nav-link <?php if($page=='admin_academy.php') echo 'active'; ?>">
                <i class="fas fa-graduation-cap"></i> <span>الأكاديمية</span>
            </a>
        <?php endif; ?>

        <?php if($my_role == 'admin'): ?>
            <div class="menu-label">إدارة النادي</div>
            <a href="admin_players.php" class="nav-link <?php if($page=='admin_players.php') echo 'active'; ?>">
                <i class="fas fa-users"></i> <span>اللاعبين والفرق</span>
            </a>
            <a href="admin_members.php" class="nav-link <?php if($page=='admin_members.php') echo 'active'; ?>">
                <i class="fas fa-user-tie"></i> <span>مجلس الإدارة</span>
            </a>
            <a href="admin_store.php" class="nav-link <?php if($page=='admin_store.php') echo 'active'; ?>">
                <i class="fas fa-tshirt"></i> <span>المتجر الإلكتروني</span>
            </a>
            
            <div class="menu-label">النظام</div>
            <a href="admin_users.php" class="nav-link <?php if($page=='admin_users.php') echo 'active'; ?>">
                <i class="fas fa-users-cog"></i> <span>الموظفين والصلاحيات</span>
            </a>
            <a href="admin_settings.php" class="nav-link <?php if($page=='admin_settings.php') echo 'active'; ?>">
                <i class="fas fa-cogs"></i> <span>الإعدادات العامة</span>
            </a>
        <?php endif; ?>
    </div>
    
    <a href="logout.php" class="nav-link" style="margin-top: 10px; color: #ff4d4d; border: 1px solid #333;">
        <i class="fas fa-sign-out-alt"></i> <span>تسجيل خروج</span>
    </a>
</div>

<div class="main-content">
    
    <div class="top-header">
        <div style="display:flex; align-items:center;">
            <div class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </div>
            <div class="header-title">
                <h4>مرحباً، <?php echo $my_name; ?> 👋</h4>
            </div>
        </div>
        
        <div class="notif-wrapper">
            <div class="notification-box" onclick="toggleNotif()">
                <i class="fas fa-bell"></i>
                <?php if($notif_count > 0): ?>
                    <span class="badge"><?php echo $notif_count; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="notif-dropdown" id="notifBox">
                <div class="notif-header">
                    <span>الإشعارات</span>
                    <?php if($notif_count > 0): ?>
                        <i class="fas fa-check-double" style="color:var(--primary); cursor:pointer;" title="تحديد كمقروء"></i>
                    <?php endif; ?>
                </div>
                <div class="notif-body">
                    <?php if($notif_count > 0): ?>
                        <div style="margin-bottom:10px;">
                            <i class="fas fa-info-circle" style="color:var(--accent);"></i>
                            <?php echo $notif_text; ?>
                        </div>
                        <a href="<?php echo $notif_link; ?>" class="notif-link">عرض التفاصيل ←</a>
                    <?php else: ?>
                        <div class="notif-empty">
                            <i class="far fa-bell-slash" style="font-size:2rem; margin-bottom:10px; display:block; opacity:0.5;"></i>
                            لا توجد إشعارات جديدة
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('adminSidebar').classList.toggle('active');
        document.getElementById('overlayAdmin').classList.toggle('active');
    }

    function toggleNotif() {
        document.getElementById('notifBox').classList.toggle('active');
    }

    window.onclick = function(event) {
        if (!event.target.closest('.notif-wrapper')) {
            var notif = document.getElementById("notifBox");
            if (notif.classList.contains('active')) {
                notif.classList.remove('active');
            }
        }
    }
</script>