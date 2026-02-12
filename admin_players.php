<?php 
include 'admin_header.php'; 

if(isset($_POST['add_player'])) {
    $img = "default_player.png";
    if($_FILES['img']['name']) {
        $img = time() . '_' . $_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'], "uploads/" . $img);
    }
    
    $stmt = $db->prepare("INSERT INTO players (name, number, role, img) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['number'], $_POST['role'], $img]);
    echo "<script>alert('تمت إضافة اللاعب'); window.location.href='admin_players.php';</script>";
}

if(isset($_GET['del'])) {
    $db->exec("DELETE FROM players WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_players.php';</script>";
}
?>

<div class="card">
    <h2>🏃 إضافة لاعب جديد</h2>
    <form method="POST" enctype="multipart/form-data">
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px;">
            <div><label>اسم اللاعب</label><input type="text" name="name" required></div>
            <div><label>رقم القميص</label><input type="number" name="number" required></div>
        </div>
        
        <label>المركز</label>
        <select name="role">
            <option>حارس مرمى</option>
            <option>مدافع</option>
            <option>خط وسط</option>
            <option>مهاجم</option>
        </select>
        
        <label>صورة اللاعب (مفرغة PNG أفضل)</label>
        <input type="file" name="img" required>
        
        <button type="submit" name="add_player" class="btn-save">إضافة للكشوفات</button>
    </form>
</div>

<div class="card">
    <h2>قائمة اللاعبين</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>الصورة</th>
                <th>الاسم</th>
                <th>المركز</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rows = $db->query("SELECT * FROM players ORDER BY number ASC")->fetchAll();
            foreach($rows as $row):
            ?>
            <tr>
                <td><?php echo $row['number']; ?></td>
                <td><img src="uploads/<?php echo $row['img']; ?>" width="40" style="border-radius:50%"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td><a href="?del=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('حذف؟')">حذف</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body></html>