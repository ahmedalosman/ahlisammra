<?php 
// استدعاء ملف الهيدر (يحتوي على الاتصال بالقاعدة والستايل والقائمة الجانبية)
include 'admin_header.php'; 

// --- كود معالجة إضافة عضو ---
if(isset($_POST['add_member'])) {
    $img_name = "default_user.png"; // صورة افتراضية
    
    // رفع الصورة
    if(isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $img_name = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], "uploads/" . $img_name);
    }
    
    // الإضافة للقاعدة
    $stmt = $db->prepare("INSERT INTO members (name, role, img) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['role'], $img_name]);
    
    echo "<script>alert('تم إضافة العضو بنجاح!'); window.location.href='admin_members.php';</script>";
}

// --- كود معالجة الحذف ---
if(isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $db->exec("DELETE FROM members WHERE id=$id");
    echo "<script>window.location.href='admin_members.php';</script>";
}
?>

<div class="card">
    <h2>👤 إدارة مجلس الإدارة</h2>
    <form method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label>الاسم الرباعي</label>
                <input type="text" name="name" required placeholder="مثال: السيد أحمد محمد">
            </div>
            <div>
                <label>المنصب</label>
                <input type="text" name="role" required placeholder="اكتب 'رئيس' في المنصب لتمييز رئيس النادي">
            </div>
        </div>
        
        <label>الصورة الشخصية (يفضل PNG مفرغة)</label>
        <input type="file" name="img" required>
        
        <button type="submit" name="add_member" class="btn-save">إضافة للقائمة</button>
    </form>
</div>

<div class="card">
    <h2>الأعضاء الحاليون</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>الصورة</th>
                <th>الاسم</th>
                <th>المنصب</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // جلب الأعضاء
            $members = $db->query("SELECT * FROM members ORDER BY id ASC")->fetchAll();
            
            if(count($members) > 0):
                foreach($members as $m):
            ?>
            <tr>
                <td><?php echo $m['id']; ?></td>
                <td>
                    <img src="uploads/<?php echo $m['img']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid var(--primary);">
                </td>
                <td style="font-weight:bold;"><?php echo $m['name']; ?></td>
                <td>
                    <?php if(strpos($m['role'], 'رئيس') !== false): ?>
                        <span style="color: gold; font-weight:bold;">👑 <?php echo $m['role']; ?></span>
                    <?php else: ?>
                        <?php echo $m['role']; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?del=<?php echo $m['id']; ?>" class="btn-del" onclick="return confirm('هل أنت متأكد من حذف هذا العضو؟');">
                        <i class="fas fa-trash-alt"></i> حذف
                    </a>
                </td>
            </tr>
            <?php 
                endforeach; 
            else:
            ?>
            <tr>
                <td colspan="5" style="text-align:center; color:#777;">لا يوجد أعضاء مضافين حالياً.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>