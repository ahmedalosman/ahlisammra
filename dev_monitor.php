<?php
session_start();

// --- ⚙️ إعدادات المطور (غيرها فوراً) ---
$DEV_PASS = "123456"; // <--- كلمة السر الخاصة بك فقط
// -------------------------------------

// معالجة تسجيل الدخول الخاص
if (isset($_POST['dev_login'])) {
    if ($_POST['password'] === $DEV_PASS) {
        $_SESSION['dev_access'] = true;
        $_SESSION['dev_ip'] = $_SERVER['REMOTE_ADDR'];
    } else {
        $login_error = "كلمة المرور غير صحيحة!";
    }
}

// معالجة تسجيل الخروج
if (isset($_GET['logout'])) {
    unset($_SESSION['dev_access']);
    header("Location: dev_monitor.php");
    exit();
}

// --- شاشة القفل (Matrix Login) ---
if (!isset($_SESSION['dev_access']) || $_SESSION['dev_access'] !== true) {
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منطقة محظورة | Dev Terminal</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #000; color: #0f0; font-family: 'Tajawal', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; overflow: hidden; }
        .login-box { border: 1px solid #0f0; padding: 40px; width: 400px; text-align: center; box-shadow: 0 0 20px #0f0; position: relative; background: rgba(0,20,0,0.8); backdrop-filter: blur(5px); }
        .login-box::before { content: "نظام المطورين"; position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #000; padding: 0 10px; font-weight: bold; border: 1px solid #0f0; }
        input { background: #111; border: 1px solid #0f0; color: #fff; padding: 12px; width: 80%; margin-top: 20px; font-family: inherit; text-align: center; font-size: 1.2rem; outline: none; border-radius: 5px; }
        button { background: #0f0; color: #000; border: none; padding: 12px 20px; margin-top: 20px; font-weight: bold; cursor: pointer; font-family: inherit; width: 60%; transition: 0.3s; border-radius: 5px; }
        button:hover { background: #fff; box-shadow: 0 0 15px #fff; }
        .error { color: #ff3333; margin-top: 15px; font-weight: bold; }
        .scan-line { position: fixed; top: 0; left: 0; width: 100%; height: 5px; background: rgba(0, 255, 0, 0.3); opacity: 0.4; animation: scan 3s linear infinite; pointer-events: none; }
        @keyframes scan { 0% { top: -10%; } 100% { top: 110%; } }
    </style>
</head>
<body>
    <div class="scan-line"></div>
    <div class="login-box">
        <h1 style="font-size:4rem; margin:0;">👁️</h1>
        <h2>لوحة التحكم السرية</h2>
        <p>الوصول مصرح للمبرمج فقط</p>
        <form method="POST">
            <input type="password" name="password" placeholder="أدخل كود الدخول" autofocus required>
            <br>
            <button type="submit" name="dev_login">فتح النظام</button>
        </form>
        <?php if(isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
    </div>
</body>
</html>
<?php
    exit();
}

// --- 🚀 بداية لوحة التحكم (بعد الدخول) ---
include 'db.php';

// 1. التحكم في إغلاق/تشغيل الموقع (Kill Switch)
if(isset($_POST['toggle_site'])) {
    $current_status = $db->query("SELECT site_status FROM settings LIMIT 1")->fetchColumn();
    $new_status = ($current_status == 1) ? 0 : 1;
    
    // تحديث الحالة
    $db->exec("UPDATE settings SET site_status = $new_status");
    
    // تسجيل العملية
    $action = ($new_status == 1) ? "تشغيل الموقع" : "إيقاف الموقع";
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, username, role, action, details, ip_address) VALUES (0, 'DEVELOPER', 'GOD_MODE', ?, 'تم تغيير الحالة من لوحة المطور', ?)");
    $stmt->execute([$action, $ip]);
    
    header("Location: dev_monitor.php");
}

// 2. مسح السجلات
if(isset($_POST['purge_logs'])) {
    $db->exec("DELETE FROM activity_logs");
    header("Location: dev_monitor.php");
}

// جلب البيانات للعرض
$settings = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$db_size = file_exists('club_v7.db') ? round(filesize('club_v7.db') / 1024, 2) . " KB" : "Unknown";

// فحص صحة الجداول
$tables_list = ['users', 'news', 'matches', 'players', 'members', 'store', 'settings', 'activity_logs', 'notifications', 'design_requests'];
$db_health = [];
foreach($tables_list as $tbl) {
    try {
        $count = $db->query("SELECT COUNT(*) FROM $tbl")->fetchColumn();
        $db_health[$tbl] = ['status' => 'OK', 'rows' => $count];
    } catch (Exception $e) {
        $db_health[$tbl] = ['status' => 'MISSING', 'rows' => 0];
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المراقب | Developer HUD</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --main: #00ff41; --bg: #050505; --panel: #111; --danger: #ff003c; --warn: #fcee0a; --text: #eee; }
        * { box-sizing: border-box; }
        body { background: var(--bg); color: var(--text); font-family: 'Tajawal', sans-serif; margin: 0; padding: 0; font-size: 14px; overflow-x: hidden; }
        
        /* التخطيط الشبكي */
        .grid-container { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        
        /* القائمة الجانبية */
        .sidebar { background: #000; border-left: 1px solid #333; padding: 20px; display: flex; flex-direction: column; height: 100vh; position: sticky; top: 0; }
        .logo { font-size: 1.2rem; border-bottom: 2px solid var(--main); padding-bottom: 10px; margin-bottom: 20px; text-align: center; color: white; font-weight: bold; }
        
        .server-info { margin-bottom: 30px; font-size: 0.9rem; color: #888; }
        .server-info div { margin-bottom: 10px; border-bottom: 1px dashed #333; padding-bottom: 5px; display: flex; justify-content: space-between; }
        .server-info span { color: var(--main); font-weight: bold; font-family: monospace; }
        
        /* قائمة فحص الجداول */
        .health-list div { padding: 8px; border: 1px solid #222; margin-bottom: 5px; background: #0a0a0a; display: flex; justify-content: space-between; font-family: monospace; }
        .status-ok { color: var(--main); }
        .status-miss { color: var(--danger); font-weight: bold; animation: pulse 1s infinite; }

        /* المحتوى الرئيسي */
        .main-view { padding: 30px; overflow-y: auto; }
        
        /* قسم الطوارئ (Kill Switch) */
        .kill-switch-box {
            background: #1a1a1a; border: 2px solid <?php echo ($settings['site_status']==1) ? 'var(--main)' : 'var(--danger)'; ?>;
            padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        .status-indicator { font-size: 1.5rem; font-weight: 900; color: <?php echo ($settings['site_status']==1) ? 'var(--main)' : 'var(--danger)'; ?>; }
        .toggle-btn {
            background: <?php echo ($settings['site_status']==1) ? 'var(--danger)' : 'var(--main)'; ?>;
            color: #000; border: none; padding: 12px 30px; font-weight: bold; font-size: 1rem; cursor: pointer; border-radius: 5px; transition: 0.3s;
        }
        .toggle-btn:hover { transform: scale(1.05); box-shadow: 0 0 15px <?php echo ($settings['site_status']==1) ? 'var(--danger)' : 'var(--main)'; ?>; }

        /* الكروت العلوية */
        .hud-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .hud-card { background: #111; border: 1px solid #333; padding: 20px; border-radius: 8px; position: relative; }
        .hud-card h3 { margin: 0 0 10px 0; font-size: 0.9rem; color: #888; }
        .hud-value { font-size: 2.2rem; font-weight: bold; color: white; }
        .hud-icon { position: absolute; left: 20px; top: 20px; font-size: 2.5rem; opacity: 0.1; }

        /* الجداول */
        h2 { color: var(--main); border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 40px; }
        .terminal-table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #333; font-family: monospace; }
        .terminal-table th { text-align: right; background: #1a1a1a; padding: 12px; color: #fff; border-bottom: 2px solid #333; }
        .terminal-table td { padding: 10px; border-bottom: 1px solid #222; color: #ccc; }
        .terminal-table tr:hover { background: #0d0d0d; }
        
        /* الألوان */
        .c-green { color: var(--main); } .c-red { color: var(--danger); } .c-yellow { color: var(--warn); } .c-blue { color: #00ccff; }

        .btn-logout { margin-top: auto; border: 1px solid var(--danger); color: var(--danger); background: transparent; padding: 10px; text-align: center; text-decoration: none; transition:0.3s; display: block; }
        .btn-logout:hover { background: var(--danger); color: white; }
        
        @keyframes pulse { 50% { opacity: 0.5; } }
        @media(max-width: 800px) { .grid-container { grid-template-columns: 1fr; } .sidebar { display: none; } }
    </style>
</head>
<body>

<div class="grid-container">
    
    <div class="sidebar">
        <div class="logo">/// غرفة العمليات ///</div>
        
        <div class="server-info">
            <div>إصدار PHP <span><?php echo phpversion(); ?></span></div>
            <div>نظام التشغيل <span><?php echo PHP_OS; ?></span></div>
            <div>حجم القاعدة <span><?php echo $db_size; ?></span></div>
            <div>الآي بي الخاص بك <span><?php echo $_SERVER['REMOTE_ADDR']; ?></span></div>
        </div>

        <h4 style="border-bottom:1px solid #333; padding-bottom:5px; color:#fff;">فحص سلامة الجداول</h4>
        <div class="health-list">
            <?php foreach($db_health as $tbl => $info): ?>
            <div>
                <?php echo $tbl; ?>
                <?php if($info['status']=='OK'): ?>
                    <span class="status-ok">[<?php echo $info['rows']; ?>] سليم</span>
                <?php else: ?>
                    <span class="status-miss">مفقود!</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <a href="?logout=true" class="btn-logout">إنهاء الجلسة الآمنة</a>
    </div>

    <div class="main-view">
        
        <div class="kill-switch-box">
            <div>
                <div style="font-size:0.9rem; color:#888; margin-bottom:5px;">حالة الموقع الحالية:</div>
                <div class="status-indicator">
                    <?php echo ($settings['site_status'] == 1) ? "✅ الموقع يعمل بشكل طبيعي" : "⛔ الموقع متوقف (وضع الصيانة)"; ?>
                </div>
            </div>
            <form method="POST">
                <button type="submit" name="toggle_site" class="toggle-btn">
                    <?php echo ($settings['site_status'] == 1) ? "إيقاف الموقع فوراً" : "إعادة تشغيل الموقع"; ?>
                </button>
            </form>
        </div>

        <div class="hud-stats">
            <div class="hud-card">
                <h3>المستخدمين المسجلين</h3>
                <div class="hud-value c-blue"><?php echo $total_users; ?></div>
                <i class="fas fa-users hud-icon"></i>
            </div>
            <div class="hud-card">
                <h3>طلبات التصميم (قيد الانتظار)</h3>
                <div class="hud-value c-yellow"><?php echo $db->query("SELECT COUNT(*) FROM design_requests WHERE status='pending'")->fetchColumn(); ?></div>
                <i class="fas fa-clock hud-icon"></i>
            </div>
            <div class="hud-card">
                <h3>إجمالي السجلات</h3>
                <div class="hud-value c-green"><?php echo $db->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn(); ?></div>
                <i class="fas fa-database hud-icon"></i>
            </div>
        </div>

        <h2>> أحدث طلبات التصميم (Design Requests)</h2>
        <div style="overflow-x:auto;">
            <table class="terminal-table">
                <thead>
                    <tr>
                        <th>الطلب</th>
                        <th>التفاصيل</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $reqs = $db->query("SELECT * FROM design_requests ORDER BY id DESC LIMIT 5")->fetchAll();
                    if(count($reqs) > 0):
                        foreach($reqs as $r):
                    ?>
                    <tr>
                        <td style="color:#fff; font-weight:bold;"><?php echo $r['title']; ?></td>
                        <td><?php echo mb_substr($r['details'], 0, 50).'...'; ?></td>
                        <td>
                            <?php 
                            if($r['status']=='pending') echo '<span class="c-yellow">قيد الانتظار</span>';
                            elseif($r['status']=='done') echo '<span class="c-blue">تم التصميم</span>';
                            else echo '<span class="c-green">معتمد</span>';
                            ?>
                        </td>
                        <td><?php echo $r['created_at']; ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4" style="text-align:center;">لا توجد طلبات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2>> سجل نشاط النظام (System Logs)</h2>
            <form method="POST" onsubmit="return confirm('هل أنت متأكد من مسح كل السجلات؟');">
                <button name="purge_logs" style="background:#300; color:red; border:1px solid red; cursor:pointer;">[ مسح السجل ]</button>
            </form>
        </div>
        
        <div style="overflow-x:auto;">
            <table class="terminal-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>الوقت</th>
                        <th>المستخدم [الدور]</th>
                        <th>الحدث</th>
                        <th>التفاصيل</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $logs = $db->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 30")->fetchAll();
                    foreach($logs as $log):
                        $c = '';
                        if(strpos($log['action'], 'دخول')!==false) $c = 'c-green';
                        if(strpos($log['action'], 'حذف')!==false || strpos($log['action'], 'إيقاف')!==false) $c = 'c-red';
                        if(strpos($log['action'], 'إضافة')!==false || strpos($log['action'], 'تشغيل')!==false) $c = 'c-yellow';
                    ?>
                    <tr>
                        <td>#<?php echo $log['id']; ?></td>
                        <td><?php echo $log['created_at']; ?></td>
                        <td><?php echo $log['username']; ?> <small style="color:#777;">[<?php echo $log['role']; ?>]</small></td>
                        <td class="<?php echo $c; ?>"><?php echo $log['action']; ?></td>
                        <td><?php echo $log['details']; ?></td>
                        <td><?php echo $log['ip_address']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>