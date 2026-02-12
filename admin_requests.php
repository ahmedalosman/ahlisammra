<?php 
include 'admin_header.php'; 

// توحيد اسم المتغير لتجنب الأخطاء
$role = $my_role; // $my_role قادم من admin_header.php

// --- 1. إذا كان إعلامي (طلب تصميم) ---
if(isset($_POST['req_design'])) {
    $stmt = $db->prepare("INSERT INTO design_requests (requester_id, title, details, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$_SESSION['admin_id'], $_POST['title'], $_POST['details']]);
    echo "<script>alert('تم إرسال الطلب للمصممين!'); window.location.href='admin_requests.php';</script>";
}

// --- 2. إذا كان مصمم (رفع التصميم) ---
if(isset($_POST['upload_design'])) {
    $img = time() . '_' . $_FILES['design_file']['name'];
    move_uploaded_file($_FILES['design_file']['tmp_name'], "uploads/" . $img);
    
    $stmt = $db->prepare("UPDATE design_requests SET design_img=?, status='done' WHERE id=?");
    $stmt->execute([$img, $_POST['req_id']]);
    echo "<script>alert('عاشق يدك! تم إرسال التصميم للإعلامي.'); window.location.href='admin_requests.php';</script>";
}

// --- 3. إذا كان إعلامي (نشر/موافقة) ---
if(isset($_GET['approve'])) {
    $stmt = $db->prepare("UPDATE design_requests SET status='approved' WHERE id=?");
    $stmt->execute([$_GET['approve']]);
    echo "<script>alert('تم اعتماد التصميم!'); window.location.href='admin_requests.php';</script>";
}
?>

<?php if($role == 'media' || $role == 'admin'): ?>
<div class="card">
    <h2>🎨 طلب تصميم جديد (للإعلاميين)</h2>
    <form method="POST">
        <label>عنوان التصميم (مثال: بوست مباراة سامراء والزوراء)</label>
        <input type="text" name="title" required>
        
        <label>التفاصيل (الألوان، النصوص المطلوبة، الصور المرفقة)</label>
        <textarea name="details" rows="4" required placeholder="اكتب كل التفاصيل للمصمم هنا..."></textarea>
        
        <button type="submit" name="req_design" class="btn-save">إرسال الطلب للمصمم</button>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <h2>حالة الطلبات</h2>
    <div style="display:grid; gap:20px;">
        <?php
        // جلب الطلبات
        if($role == 'designer') {
            // المصمم يرى الطلبات المعلقة أولاً
            $reqs = $db->query("SELECT * FROM design_requests WHERE status IN ('pending', 'done') ORDER BY id DESC")->fetchAll();
        } else {
            // الإعلامي يرى طلباته
            $reqs = $db->query("SELECT * FROM design_requests ORDER BY id DESC")->fetchAll();
        }

        if(count($reqs) > 0):
            foreach($reqs as $r):
        ?>
        <div style="background:#2a2a2a; padding:20px; border-radius:10px; border-right:4px solid <?php echo ($r['status']=='done'?'#00ff00':($r['status']=='pending'?'orange':'gray')); ?>;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; color:white;"><?php echo $r['title']; ?></h3>
                <span style="color:#aaa; font-size:0.8rem; background:#111; padding:3px 8px; border-radius:5px;"><?php echo $r['created_at']; ?></span>
            </div>
            <p style="color:#ccc; margin:10px 0; border-top:1px solid #444; padding-top:10px;"><?php echo nl2br($r['details']); ?></p>
            
            <?php if($role == 'designer' && $r['status'] == 'pending'): ?>
                <div style="background:#111; padding:15px; border-radius:8px; margin-top:15px;">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="req_id" value="<?php echo $r['id']; ?>">
                        <label style="color:orange; font-size:0.9rem;">📤 رفع التصميم الجاهز:</label>
                        <input type="file" name="design_file" required style="margin-bottom:10px;">
                        <button type="submit" name="upload_design" class="btn-save" style="background:#333; border:1px solid orange; color:orange;">تسليم العمل</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if($r['status'] == 'done'): ?>
                <div style="background:#1a1a1a; padding:15px; margin-top:15px; border-radius:10px; border:1px solid #333;">
                    <p style="color:#00ff00; font-weight:bold;">✅ المصمم أنجز العمل:</p>
                    <img src="uploads/<?php echo $r['design_img']; ?>" style="max-width:100%; height:200px; object-fit:contain; border:1px solid #444; margin:10px 0; background:#000;">
                    
                    <?php if($role == 'media' || $role == 'admin'): ?>
                        <div style="margin-top:10px; display:flex; gap:10px;">
                            <a href="uploads/<?php echo $r['design_img']; ?>" download class="btn-save" style="text-align:center; background:#444; width:auto; flex:1;">تحميل</a>
                            <a href="?approve=<?php echo $r['id']; ?>" class="btn-save" style="text-align:center; background:green; width:auto; flex:1;">موافقة واعتماد</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($r['status'] == 'approved'): ?>
                <p style="color:#777; font-size:0.8rem; margin-top:10px;"> <i class="fas fa-check-circle"></i> تم الاعتماد والأرشفة.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; color:#777;">لا توجد طلبات حالياً.</p>
        <?php endif; ?>
    </div>
</div>
</body></html>