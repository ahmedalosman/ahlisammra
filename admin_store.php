<?php 
include 'admin_header.php'; 

if(isset($_POST['add_product'])) {
    $img = "";
    if($_FILES['img']['name']) {
        $img = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], "uploads/" . $img);
    }
    
    $stmt = $db->prepare("INSERT INTO store (name, price, img) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['price'], $img]);
    echo "<script>alert('تمت إضافة المنتج'); window.location.href='admin_store.php';</script>";
}

if(isset($_GET['del'])) {
    $db->exec("DELETE FROM store WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_store.php';</script>";
}
?>

<div class="card">
    <h2>🛒 إضافة منتج للمتجر</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>اسم المنتج</label>
        <input type="text" name="name" required placeholder="مثال: قميص 2025">
        
        <label>السعر (د.ع)</label>
        <input type="text" name="price" required placeholder="مثال: 25,000">
        
        <label>صورة المنتج</label>
        <input type="file" name="img" required>
        
        <button type="submit" name="add_product" class="btn-save">عرض في المتجر</button>
    </form>
</div>

<div class="card">
    <h2>المنتجات الحالية</h2>
    <table>
        <thead>
            <tr>
                <th>الصورة</th>
                <th>الاسم</th>
                <th>السعر</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rows = $db->query("SELECT * FROM store")->fetchAll();
            foreach($rows as $row):
            ?>
            <tr>
                <td><img src="uploads/<?php echo $row['img']; ?>" width="50"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><a href="?del=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('حذف؟')">حذف</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body></html>