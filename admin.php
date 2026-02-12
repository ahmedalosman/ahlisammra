<?php include 'admin_header.php'; ?>

<div class="card">
    <h2>👋 أهلاً بك في لوحة القيادة</h2>
    <p>اختر قسماً من القائمة الجانبية للبدء في إدارة محتوى الموقع.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
    <div class="card" style="text-align:center;">
        <i class="fas fa-newspaper" style="font-size: 2rem; color: #e0aaff;"></i>
        <h3><?php echo $db->query("SELECT COUNT(*) FROM news")->fetchColumn(); ?></h3>
        <p>أخبار منشورة</p>
    </div>
    <div class="card" style="text-align:center;">
        <i class="fas fa-users" style="font-size: 2rem; color: #e0aaff;"></i>
        <h3><?php echo $db->query("SELECT COUNT(*) FROM players")->fetchColumn(); ?></h3>
        <p>لاعبين</p>
    </div>
    <div class="card" style="text-align:center;">
        <i class="fas fa-shopping-cart" style="font-size: 2rem; color: #e0aaff;"></i>
        <h3><?php echo $db->query("SELECT COUNT(*) FROM store")->fetchColumn(); ?></h3>
        <p>منتجات</p>
    </div>
</div>

</body>
</html>