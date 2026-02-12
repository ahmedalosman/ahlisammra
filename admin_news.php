<?php 
include 'admin_header.php'; 

// إضافة خبر
if(isset($_POST['add_news'])) {
    $img = "";
    if($_FILES['img']['name']) {
        $img = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], "uploads/" . $img);
    }
    
    $is_hero = isset($_POST['is_hero']) ? 1 : 0;
    
    $stmt = $db->prepare("INSERT INTO news (title, cat, content, img, is_hero) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['cat'], $_POST['content'], $img, $is_hero]);
    echo "<script>alert('تم نشر الخبر بنجاح'); window.location.href='admin_news.php';</script>";
}

// حذف خبر
if(isset($_GET['del'])) {
    $db->exec("DELETE FROM news WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_news.php';</script>";
}
?>

<div class="card">
    <h2>📰 نشر خبر جديد</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>عنوان الخبر</label>
        <input type="text" name="title" required>
        
        <label>التصنيف</label>
        <select name="cat">
            <option>أخبار النادي</option>
            <option>الفريق الأول</option>
            <option>بيان رسمي</option>
            <option>الأكاديمية</option>
        </select>
        
        <label>التفاصيل</label>
        <textarea name="content" rows="5" required></textarea>
        
        <label>صورة الخبر (يفضل عرضية)</label>
        <input type="file" name="img" required>
        
        <div style="margin: 15px 0; background: #333; padding: 10px; border-radius: 5px;">
            <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="is_hero" style="width:20px; height:20px;">
                عرض في السلايدر الرئيسي (الواجهة الكبيرة)؟
            </label>
        </div>

        <button type="submit" name="add_news" class="btn-save">نشر الخبر</button>
    </form>
</div>

<div class="card">
    <h2>أرشيف الأخبار</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>الصورة</th>
                <th>العنوان</th>
                <th>التصنيف</th>
                <th>واجهة؟</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rows = $db->query("SELECT * FROM news ORDER BY id DESC")->fetchAll();
            foreach($rows as $row):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><img src="uploads/<?php echo $row['img']; ?>" width="50"></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['cat']; ?></td>
                <td><?php echo $row['is_hero'] ? '✅' : '❌'; ?></td>
                <td><a href="?del=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('حذف؟')">حذف</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body></html>