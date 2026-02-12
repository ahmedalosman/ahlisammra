<?php 
include 'admin_header.php'; // تأكد أن هذا الملف موجود كما أنشأناه سابقاً

// إضافة قسم جديد
if(isset($_POST['add_prog'])) {
    $img = "";
    if($_FILES['img']['name']) {
        $img = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], "uploads/" . $img);
    }
    
    $stmt = $db->prepare("INSERT INTO academy (title, content, img) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['content'], $img]);
    echo "<script>alert('تم إضافة البرنامج'); window.location.href='admin_academy.php';</script>";
}

// حذف قسم
if(isset($_GET['del'])) {
    $db->exec("DELETE FROM academy WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_academy.php';</script>";
}
?>

<div class="card">
    <h2>🎓 إدارة برامج الأكاديمية</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>عنوان البرنامج (مثال: فئة البراعم)</label>
        <input type="text" name="title" required>
        
        <label>وصف البرنامج</label>
        <textarea name="content" rows="4" required placeholder="اكتب نبذة عن التدريبات والأعمار المستهدفة..."></textarea>
        
        <label>صورة معبرة</label>
        <input type="file" name="img" required>
        
        <button type="submit" name="add_prog" class="btn-save">نشر البرنامج</button>
    </form>
</div>

<div class="card">
    <h2>البرامج الحالية</h2>
    <table>
        <thead>
            <tr>
                <th>الصورة</th>
                <th>العنوان</th>
                <th>الوصف</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rows = $db->query("SELECT * FROM academy")->fetchAll();
            foreach($rows as $row):
            ?>
            <tr>
                <td><img src="uploads/<?php echo $row['img']; ?>" width="60" style="border-radius:5px;"></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo mb_substr($row['content'], 0, 50).'...'; ?></td>
                <td><a href="?del=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('حذف؟')">حذف</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body></html>