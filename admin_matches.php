<?php 
include 'admin_header.php'; 

// --- دالة الممحاة الذكية (Remove Background) ---
function upload_and_remove_bg($file) {
    $uploadDir = "uploads/";
    $check = getimagesize($file["tmp_name"]);
    if($check === false) return false;

    $new_name = time() . "_" . uniqid() . ".png"; 
    $target_file = $uploadDir . $new_name;

    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if ($ext == 'jpg' || $ext == 'jpeg') $im = imagecreatefromjpeg($file["tmp_name"]);
    elseif ($ext == 'png') $im = imagecreatefrompng($file["tmp_name"]);
    elseif ($ext == 'gif') $im = imagecreatefromgif($file["tmp_name"]);
    else return false;

    $bg_color = imagecolorat($im, 0, 0);
    imagecolortransparent($im, $bg_color);
    imagepng($im, $target_file);
    imagedestroy($im);

    return $new_name;
}

// متغيرات لتخزين بيانات المباراة عند التعديل
$edit_mode = false;
$m_data = [
    'opp_name' => '', 'match_time' => '', 'stadium' => '', 
    'status' => 'upcoming', 'home_score' => 0, 'opp_score' => 0, 'scorers' => ''
];

// --- 1. جلب بيانات المباراة عند طلب التعديل ---
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM matches WHERE id = ?");
    $stmt->execute([$id]);
    $m_data = $stmt->fetch();
    if($m_data) $edit_mode = true;
}

// --- 2. معالجة الحفظ (إضافة جديدة) ---
if(isset($_POST['add_match'])) {
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo = upload_and_remove_bg($_FILES['logo']);
    } else {
        $logo = "default_team.png";
    }
    
    if($logo) {
        $stmt = $db->prepare("INSERT INTO matches (opp_name, opp_logo, match_time, stadium, status, home_score, opp_score, scorers) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['opp_name'], $logo, $_POST['time'], $_POST['stadium'], 
            $_POST['status'], $_POST['home_score'], $_POST['opp_score'], $_POST['scorers']
        ]);
        echo "<script>alert('تم إضافة المباراة بنجاح!'); window.location.href='admin_matches.php';</script>";
    }
}

// --- 3. معالجة التحديث (تعديل النتيجة أو البيانات) ---
if(isset($_POST['update_match'])) {
    $id = $_POST['match_id'];
    
    // هل تم رفع شعار جديد؟
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo = upload_and_remove_bg($_FILES['logo']); // معالجة الشعار الجديد
        // تحديث مع الصورة
        $sql = "UPDATE matches SET opp_name=?, opp_logo=?, match_time=?, stadium=?, status=?, home_score=?, opp_score=?, scorers=? WHERE id=?";
        $params = [$_POST['opp_name'], $logo, $_POST['time'], $_POST['stadium'], $_POST['status'], $_POST['home_score'], $_POST['opp_score'], $_POST['scorers'], $id];
    } else {
        // تحديث بدون تغيير الصورة القديمة
        $sql = "UPDATE matches SET opp_name=?, match_time=?, stadium=?, status=?, home_score=?, opp_score=?, scorers=? WHERE id=?";
        $params = [$_POST['opp_name'], $_POST['time'], $_POST['stadium'], $_POST['status'], $_POST['home_score'], $_POST['opp_score'], $_POST['scorers'], $id];
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    echo "<script>alert('تم تحديث بيانات المباراة والنتيجة!'); window.location.href='admin_matches.php';</script>";
}

// حذف
if(isset($_GET['del'])) {
    $db->exec("DELETE FROM matches WHERE id=".$_GET['del']);
    echo "<script>window.location.href='admin_matches.php';</script>";
}
?>

<div class="card">
    <h2>
        <?php echo $edit_mode ? '✏️ تعديل نتيجة / تفاصيل المباراة' : '⚽ إضافة مباراة جديدة'; ?>
    </h2>
    <p style="color:#aaa; font-size:0.9rem; margin-bottom:20px;">
        <?php echo $edit_mode ? 'قم بتغيير الحالة إلى "منتهية" لإدخال النتيجة والهدافين.' : 'أدخل بيانات المباراة القادمة، ويمكنك تعديل النتيجة لاحقاً.'; ?>
    </p>
    
    <form method="POST" enctype="multipart/form-data">
        <?php if($edit_mode): ?>
            <input type="hidden" name="match_id" value="<?php echo $m_data['id']; ?>">
        <?php endif; ?>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div>
                <label>اسم الخصم</label>
                <input type="text" name="opp_name" required value="<?php echo $m_data['opp_name']; ?>" placeholder="مثال: القوة الجوية">
            </div>
            <div>
                <label>شعار الخصم <?php if($edit_mode) echo '(اتركه فارغاً للإبقاء على القديم)'; ?></label>
                <input type="file" name="logo" accept="image/*" <?php if(!$edit_mode) echo 'required'; ?>>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div>
                <label>التوقيت</label>
                <input type="datetime-local" name="time" required value="<?php echo $edit_mode ? date('Y-m-d\TH:i', strtotime($m_data['match_time'])) : ''; ?>">
            </div>
            <div>
                <label>الملعب</label>
                <input type="text" name="stadium" value="<?php echo $m_data['stadium']; ?>" placeholder="مثال: ملعب سامراء">
            </div>
        </div>

        <label>حالة المباراة</label>
        <select name="status" style="background:#222; color:white; padding:10px; width:100%; border:1px solid #444;">
            <option value="upcoming" <?php if($m_data['status']=='upcoming') echo 'selected'; ?>>قادمة (لم تبدأ)</option>
            <option value="live" <?php if($m_data['status']=='live') echo 'selected'; ?>>جارية الآن (مباشر 🔴)</option>
            <option value="finished" <?php if($m_data['status']=='finished') echo 'selected'; ?>>منتهية (تسجيل النتيجة ✅)</option>
        </select>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:15px; background:#1a1a1a; padding:15px; border-radius:10px;">
            <div><label style="color:#e0aaff;">أهدافنا</label><input type="number" name="home_score" value="<?php echo $m_data['home_score']; ?>"></div>
            <div><label style="color:#e0aaff;">أهداف الخصم</label><input type="number" name="opp_score" value="<?php echo $m_data['opp_score']; ?>"></div>
        </div>

        <label>مسجلي الأهداف (للمباريات المنتهية)</label>
        <textarea name="scorers" placeholder="مثال: أحمد (15), علي (80)"><?php echo $m_data['scorers']; ?></textarea>

        <?php if($edit_mode): ?>
            <button type="submit" name="update_match" class="btn-save" style="background:#007bff;">💾 حفظ التعديلات والنتيجة</button>
            <a href="admin_matches.php" class="btn-save" style="background:#555; text-align:center; display:block; margin-top:10px; text-decoration:none;">إلغاء</a>
        <?php else: ?>
            <button type="submit" name="add_match" class="btn-save">➕ إضافة المباراة</button>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h2>جدول المباريات</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>الخصم</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>النتيجة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rows = $db->query("SELECT * FROM matches ORDER BY match_time DESC")->fetchAll();
                foreach($rows as $row):
                ?>
                <tr>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <div style="background:#333; padding:5px; border-radius:50%; width:40px; height:40px; display:flex; justify-content:center; align-items:center;">
                            <img src="uploads/<?php echo $row['opp_logo']; ?>" style="max-width:100%; max-height:100%;">
                        </div>
                        <?php echo $row['opp_name']; ?>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['match_time'])); ?></td>
                    <td>
                        <?php 
                        if($row['status']=='live') echo '<span style="color:red; font-weight:bold;">مباشر</span>';
                        elseif($row['status']=='finished') echo '<span style="color:#00ff00;">منتهية</span>';
                        else echo 'قادمة';
                        ?>
                    </td>
                    <td style="font-weight:bold; font-size:1.1rem;"><?php echo $row['home_score'] . ' - ' . $row['opp_score']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['id']; ?>" class="btn-del" style="background:rgba(0,123,255,0.2); color:#007bff; margin-left:5px;">
                            <i class="fas fa-pen"></i> تعديل
                        </a>
                        <a href="?del=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('حذف المباراة؟')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body></html>