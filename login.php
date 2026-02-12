<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$user, $pass]);
    $u = $stmt->fetch();

    if ($u) {
        $_SESSION['admin_id'] = $u['id'];
        $_SESSION['role'] = $u['role']; // حفظ الصلاحية في الجلسة
        $_SESSION['name'] = $u['full_name'];
        header("Location: admin.php");
        exit();
    } else {
        $error = "بيانات الدخول غير صحيحة!";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <style>
        body { background: #000; color: white; display: flex; height: 100vh; justify-content: center; align-items: center; font-family: 'Tajawal', sans-serif; }
        .box { background: #111; padding: 40px; border-radius: 15px; border: 1px solid #4b0082; width: 350px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #222; border: 1px solid #333; color: white; border-radius: 5px; }
        button { background: #4b0082; color: white; padding: 12px; width: 100%; border: none; cursor: pointer; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <img src="icon.png" width="80" style="margin-bottom:20px;">
        <h2>نظام إدارة النادي</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit" name="login">دخول</button>
        </form>
    </div>
</body>
</html>