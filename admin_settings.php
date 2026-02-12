<?php 
include 'admin_header.php'; 

if(isset($_POST['save_settings'])) {
    $active = isset($_POST['ticker_active']) ? 1 : 0;
    $stmt = $db->prepare("UPDATE settings SET ticker_active=?, ticker_text=?");
    $stmt->execute([$active, $_POST['ticker_text']]);
    echo "<script>alert('تم حفظ الإعدادات'); window.location.href='admin_settings.php';</script>";
}

$sets = $db->query("SELECT * FROM settings LIMIT 1")->fetch();
?>

<div class="card">
    <h2>⚙️ إعدادات الموقع العامة</h2>
    <form method="POST">
        <label>نص الشريط الإخباري المتحرك</label>
        <input type="text" name="ticker_text" value="<?php echo $sets['ticker_text']; ?>">
        
        <div style="margin: 20px 0; background: #333; padding: 15px; border-radius: 5px;">
            <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="ticker_active" style="width:20px; height:20px;" <?php echo $sets['ticker_active']?'checked':''; ?>>
                تفعيل الشريط الإخباري في أعلى الموقع
            </label>
        </div>
        
        <button type="submit" name="save_settings" class="btn-save">حفظ التغييرات</button>
    </form>
</div>

</body></html>