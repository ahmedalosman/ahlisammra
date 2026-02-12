<?php 
include 'admin_header.php'; 

// حماية الصفحة للمدير فقط
if($_SESSION['role'] != 'admin') {
    echo "<div class='card'><h1>⛔ غير مصرح لك بدخول هذه الصفحة</h1></div>";
    exit();
}

// إضافة مستخدم
if(isset($_POST['add_user'])) {
    $stmt = $db->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['full_name'], $_POST['username'], $_POST['password'], $_POST['role']]);
    echo "<script>alert('تم إضافة المستخدم'); window.location.href='admin_users.php';</script>";
}

// حذف
if(isset($_GET['del'])) {
    $db->exec("DELETE FROM users WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_users.php';</script>";
}
?>

<div class="card">
    <h2>👤 إنشاء حساب جديد (موظف)</h2>
    <form method="POST">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div>
                <label>الاسم الكامل</label>
                <input type="text" name="full_name" required placeholder="مثال: محمد علي">
            </div>
            <div>
                <label>نوع الصلاحية</label>
                <select name="role">
                    <option value="media">إعلامي (نشر أخبار + طلب تصاميم)</option>
                    <option value="designer">مصمم (استلام طلبات + رفع تصاميم)</option>
                    <option value="admin">مدير عام (صلاحية كاملة)</option>
                </select>
            </div>
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div><label>اسم المستخدم (للدخول)</label><input type="text" name="username" required></div>
            <div><label>كلمة المرور</label><input type="text" name="password" required></div>
        </div>
        <button type="submit" name="add_user" class="btn-save">إنشاء الحساب</button>
    </form>
</div>

<div class="card">
    <h2>المستخدمين الحاليين</h2>
    <table style="width:100%; text-align:right;">
        <tr style="background:#111;"><th>الاسم</th><th>اليوزر</th><th>الدور</th><th>إجراء</th></tr>
        <?php
        $users = $db->query("SELECT * FROM users")->fetchAll();
        foreach($users as $u):
        ?>
        <tr style="border-bottom:1px solid #333;">
            <td style="padding:15px;"><?php echo $u['full_name']; ?></td>
            <td><?php echo $u['username']; ?></td>
            <td>
                <?php 
                if($u['role']=='admin') echo '<span style="color:gold">مدير</span>';
                elseif($u['role']=='designer') echo '<span style="color:#e0aaff">مصمم</span>';
                else echo '<span style="color:cyan">إعلامي</span>';
                ?>
            </td>
            <td><a href="?del=<?php echo $u['id']; ?>" style="color:red;" onclick="return confirm('حذف؟')">حذف</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body></html>