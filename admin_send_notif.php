<?php 
include 'admin_header.php'; 

// حماية الصفحة: للمدير فقط
if($_SESSION['role'] != 'admin') {
    echo "<div class='card' style='text-align:center; padding:50px;'>
            <h1 style='color:red;'>⛔ عذراً، هذه الصفحة للمدير العام فقط.</h1>
          </div>";
    exit();
}

// معالجة الإرسال
if(isset($_POST['send_alert'])) {
    $target = $_POST['target_role'];
    $msg = $_POST['message'];
    $sender = $_SESSION['admin_id'];
    
    $stmt = $db->prepare("INSERT INTO notifications (sender_id, target_role, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender, $target, $msg]);
    
    echo "<script>alert('تم تعميم الإشعار بنجاح! 📢'); window.location.href='admin_send_notif.php';</script>";
}

// حذف إشعار قديم
if(isset($_GET['del'])) {
    $db->exec("DELETE FROM notifications WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_send_notif.php';</script>";
}
?>

<div class="card">
    <h2>📢 مركز التعاميم والإشعارات</h2>
    <p style="color:#aaa; margin-bottom:20px;">يمكنك من هنا إرسال توجيهات إدارية تظهر فوراً في لوحة تحكم الموظفين.</p>
    
    <form method="POST">
        <label>إلى من تريد إرسال الإشعار؟</label>
        <select name="target_role" required>
            <option value="all">📢 الجميع (إعلاميين ومصممين)</option>
            <option value="media">📷 الإعلاميين فقط</option>
            <option value="designer">🎨 المصممين فقط</option>
        </select>
        
        <label>نص الرسالة / التوجيه</label>
        <textarea name="message" rows="4" required placeholder="مثال: يرجى الحضور للاجتماع غداً الساعة 4 عصراً..."></textarea>
        
        <button type="submit" name="send_alert" class="btn-save">إرسال التعميم</button>
    </form>
</div>

<div class="card">
    <h2>سجل الإشعارات المرسلة</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>المستلم</th>
                    <th>الرسالة</th>
                    <th>وقت الإرسال</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $alerts = $db->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 20")->fetchAll();
                foreach($alerts as $a):
                ?>
                <tr>
                    <td>
                        <?php 
                        if($a['target_role']=='all') echo '<span class="badge" style="background:purple;">الكل</span>';
                        elseif($a['target_role']=='media') echo '<span class="badge" style="background:cyan; color:black;">إعلام</span>';
                        else echo '<span class="badge" style="background:orange; color:black;">تصميم</span>';
                        ?>
                    </td>
                    <td><?php echo $a['message']; ?></td>
                    <td style="font-size:0.8rem; color:#aaa;"><?php echo $a['created_at']; ?></td>
                    <td><a href="?del=<?php echo $a['id']; ?>" class="btn-del" onclick="return confirm('حذف؟')">حذف</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>