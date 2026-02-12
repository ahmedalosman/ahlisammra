<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تاريخ النادي | أهلي سامراء</title>
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* --- المتغيرات والتأسيس --- */
        :root {
            --primary: #4b0082;       /* بنفسجي */
            --primary-light: #6a0dad;
            --accent: #e0aaff;        /* ليلكي فاتح */
            --gold: #ffd700;          /* ذهبي */
            --dark: #050505;          /* أسود */
            --card-bg: #111;
        }

        * { box-sizing: border-box; }
        body { background-color: var(--dark); color: white; font-family: 'Tajawal', sans-serif; overflow-x: hidden; margin: 0; }

        /* --- 1. قسم الواجهة (Hero Section) --- */
        .history-hero {
            position: relative;
            min-height: 100vh; /* ملء الشاشة */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            /* خلفية مع طبقة تظليل */
            background: linear-gradient(to bottom, rgba(0,0,0,0.6), var(--dark)), url('d.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* بارالكس */
            padding: 20px;
            overflow: hidden;
        }

        /* عناصر الهيرو */
        .kt-brand {
            width: 180px;
            filter: drop-shadow(0 0 15px rgba(255,255,255,0.3));
            animation: float 4s ease-in-out infinite;
            margin-bottom: 20px;
        }

        .club-logo-hero {
            width: 140px;
            filter: drop-shadow(0 0 30px var(--primary));
            margin-bottom: 20px;
            animation: zoomIn 1.5s ease-out;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 5rem); /* خط متجاوب */
            font-weight: 900;
            margin: 10px 0;
            background: linear-gradient(to right, #fff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .hero-subtitle {
            font-size: clamp(1rem, 3vw, 1.5rem);
            color: #ddd;
            max-width: 600px;
            line-height: 1.6;
            margin-top: 10px;
            border-top: 1px solid var(--primary);
            padding-top: 20px;
        }

        /* --- 2. قسم القصة (Story) --- */
        .story-section {
            padding: 80px 10%;
            display: flex;
            align-items: center;
            gap: 60px;
            background: linear-gradient(to bottom, var(--dark), #0f0f0f);
            position: relative;
        }

        .story-content { flex: 1; position: relative; z-index: 2; }
        .story-image { flex: 1; position: relative; }
        
        .year-badge {
            font-size: clamp(4rem, 8vw, 6rem);
            font-weight: 900;
            color: transparent;
            -webkit-text-stroke: 2px var(--primary);
            opacity: 0.3;
            position: absolute;
            top: -40px;
            right: -20px;
            z-index: -1;
        }

        .story-title { font-size: 2rem; color: var(--gold); margin-bottom: 20px; position: relative; display: inline-block; }
        .story-title::after { content:''; display: block; width: 50%; height: 3px; background: var(--primary); margin-top: 10px; }
        
        .story-text { line-height: 1.9; color: #ccc; font-size: 1.1rem; text-align: justify; }

        .img-frame {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 20px 20px 0 var(--primary);
            border: 2px solid #333;
            transform: rotate(-2deg);
            transition: 0.4s;
        }
        .img-frame:hover { transform: rotate(0); box-shadow: 10px 10px 0 var(--accent); }
        .img-frame img { width: 100%; height: auto; display: block; }

        /* --- 3. قسم القائمين على النادي (جديد) --- */
        .founders-section { padding: 80px 5%; background: #080808; text-align: center; }
        .founders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        .founder-card {
            background: #111;
            padding: 30px;
            border-radius: 15px;
            border-bottom: 3px solid var(--primary);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }
        .founder-card:hover { transform: translateY(-10px); background: #161616; }
        
        .f-icon { 
            width: 80px; height: 80px; background: var(--primary); color: white;
            border-radius: 50%; display: flex; justify-content: center; align-items: center;
            font-size: 2rem; margin: 0 auto 20px; box-shadow: 0 0 20px rgba(75, 0, 130, 0.5);
        }
        .founder-role { color: var(--accent); font-size: 0.9rem; letter-spacing: 1px; margin-bottom: 5px; display: block; }
        .founder-name { color: white; font-size: 1.4rem; font-weight: bold; margin: 0; }

        /* --- 4. قسم القيم --- */
        .values-section { padding: 60px 5%; text-align: center; background: var(--dark); }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .value-card {
            background: #111; padding: 30px; border-radius: 15px; border: 1px solid #222;
            transition: 0.3s;
        }
        .value-card:hover { border-color: var(--primary); background: linear-gradient(45deg, #111, #1a002e); }
        .v-icon { font-size: 2.5rem; color: var(--gold); margin-bottom: 15px; }

        /* --- 5. معرض الصور --- */
        .gallery-section { padding: 0; }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
        .g-item { height: 300px; overflow: hidden; position: relative; cursor: pointer; }
        .g-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); filter: grayscale(80%); }
        .g-item:hover img { transform: scale(1.1); filter: grayscale(0%); }
        
        /* تراكب نصي على الصورة */
        .g-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%; padding: 20px;
            background: linear-gradient(to top, black, transparent);
            opacity: 0; transition: 0.3s;
        }
        .g-item:hover .g-overlay { opacity: 1; }

        /* --- تحسينات الموبايل (Responsive) --- */
        @media (max-width: 768px) {
            .history-hero { background-attachment: scroll; /* إلغاء البارالكس في الموبايل للأداء */ padding-top: 80px; }
            .kt-brand { width: 120px; }
            .club-logo-hero { width: 100px; }
            
            .story-section { flex-direction: column-reverse; padding: 50px 5%; gap: 40px; }
            .year-badge { top: 0; right: 0; font-size: 4rem; position: relative; display: block; text-align: center; width: 100%; margin-bottom: 10px; }
            .story-title { font-size: 1.8rem; text-align: center; display: block; }
            .story-title::after { margin: 10px auto; }
            .story-text { text-align: center; font-size: 1rem; }
            
            .img-frame { transform: rotate(0); box-shadow: 10px 10px 0 var(--primary); margin: 0 auto; max-width: 90%; }
            
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .g-item { height: 200px; }
        }

        /* حركات بسيطة */
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }
        @keyframes zoomIn { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    </style>
</head>
<body>

<section class="history-hero">
    <div data-aos="fade-down" data-aos-duration="1000">
        <img src="kt.png" alt="KT Brand" class="kt-brand">
    </div>
    
    <div data-aos="zoom-in" data-aos-delay="200">
        <img src="icon.png" alt="Ahli Samarra" class="club-logo-hero">
    </div>
    
    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="400">أهلي سامراء</h1>
    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="600">
        حكاية مجد.. من قلب الملوية إلى منصات التتويج
    </p>
</section>

<section class="story-section">
    <div class="story-image" data-aos="fade-right" data-aos-duration="1000">
        <div class="img-frame">
            <img src="DSC_7445.jpg" alt="Football Team">
        </div>
    </div>
    <div class="story-content" data-aos="fade-left" data-aos-duration="1000">
        <span class="year-badge">2025</span>
        <h2 class="story-title">ولادة العملاق الأهلاوي</h2>
        <p class="story-text">
            في قلب مدينة سامراء العريقة، مهد الحضارات، ومن رحم الشغف بكرة القدم، تأسس نادي "أهلي سامراء" في عام 2025. 
            لم تكن الفكرة مجرد إنشاء فريق كرة قدم، بل كانت رؤية متكاملة لبناء صرح رياضي يجمع شباب المدينة، ويصقل المواهب، وينافس على الألقاب منذ اليوم الأول.
            <br><br>
            برؤية إدارية ثاقبة ودعم جماهيري لا محدود، انطلق النادي ليحجز مكانه بين الكبار، متخذاً من اللون البنفسجي رمزاً للفخامة والتميز، ومن "الملوية" شعاراً للشموخ والارتفاع نحو القمة.
        </p>
    </div>
</section>

<section class="founders-section">
    <h2 class="hero-title" style="font-size: 2.5rem; margin-bottom: 10px;" data-aos="fade-up">قيادة النادي</h2>
    <p style="color:#777;" data-aos="fade-up">رؤية إدارية تصنع المستقبل</p>

    <div class="founders-grid">
        <div class="founder-card" data-aos="flip-left" data-aos-delay="100">
            <div class="f-icon"><i class="fas fa-crown"></i></div>
            <span class="founder-role">رئيس النادي</span>
            <h3 class="founder-name">الهيئة التأسيسية</h3>
            <p style="color:#888; font-size:0.9rem; margin-top:10px;">قيادة استراتيجية ورؤية طموحة.</p>
        </div>

        <div class="founder-card" data-aos="flip-left" data-aos-delay="200">
            <div class="f-icon"><i class="fas fa-whistle"></i></div>
            <span class="founder-role">الكادر الفني</span>
            <h3 class="founder-name">نخبة المدربين</h3>
            <p style="color:#888; font-size:0.9rem; margin-top:10px;">خبرات تدريبية لبناء جيل ذهبي.</p>
        </div>

        <div class="founder-card" data-aos="flip-left" data-aos-delay="300">
            <div class="f-icon"><i class="fas fa-briefcase"></i></div>
            <span class="founder-role">الإدارة واللوجستيات</span>
            <h3 class="founder-name">فريق العمل</h3>
            <p style="color:#888; font-size:0.9rem; margin-top:10px;">جنود مجهولون لخدمة الكيان.</p>
        </div>
    </div>
</section>

<section class="values-section">
    <div data-aos="zoom-in">
        <h2 style="font-size:2.2rem; color:white; margin-bottom:10px;">ركائزنا الأساسية</h2>
        <div style="width:50px; height:3px; background:var(--primary); margin:0 auto;"></div>
    </div>
    
    <div class="values-grid">
        <div class="value-card" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-bullseye v-icon"></i>
            <h3>الرؤية</h3>
            <p style="color:#ccc;">أن نكون الرقم الصعب في الكرة العراقية خلال السنوات الخمس الأولى.</p>
        </div>
        <div class="value-card" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-users v-icon"></i>
            <h3>المجتمع</h3>
            <p style="color:#ccc;">تمثيل سامراء بأفضل صورة وخلق بيئة رياضية صحية للشباب.</p>
        </div>
        <div class="value-card" data-aos="fade-up" data-aos-delay="300">
            <i class="fas fa-trophy v-icon"></i>
            <h3>الاحترافية</h3>
            <p style="color:#ccc;">تطبيق أحدث معايير الإدارة الرياضية لبناء فريق لا يقهر.</p>
        </div>
    </div>
</section>

<section class="gallery-section">
    <div class="gallery-grid">
        <div class="g-item" data-aos="fade-in">
            <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=800&auto=format&fit=crop" alt="Fans">
            <div class="g-overlay"><h4 style="color:white; margin:0;">جمهور وفي</h4></div>
        </div>
        <div class="g-item" data-aos="fade-in" data-aos-delay="100">
            <img src="https://images.unsplash.com/photo-1517466787929-bc90951d0974?q=80&w=800&auto=format&fit=crop" alt="Training">
            <div class="g-overlay"><h4 style="color:white; margin:0;">تدريبات مكثفة</h4></div>
        </div>
        <div class="g-item" data-aos="fade-in" data-aos-delay="200">
            <img src="https://scontent.fbgw66-3.fna.fbcdn.net/v/t39.30808-6/514360531_1178993727365354_7727389938232921739_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=86c6b0&_nc_ohc=hXutLATw6i8Q7kNvwG8p86f&_nc_oc=AdnhQVEUtDTh3OQTS8gRRwPhPhn5NKgiK3mqJ6afjAknEHPc0nmaTrRT5j3oOD9R-UY&_nc_zt=23&_nc_ht=scontent.fbgw66-3.fna&_nc_gid=kgH99nITlb50KIhVPgt3qg&oh=00_AfkR673xjGSHrxJBDmTXPVPszCRLsgL15I9NC2k_J9zzng&oe=695000D7" alt="Stadium">
            <div class="g-overlay"><h4 style="color:white; margin:0;">القلعة</h4></div>
        </div>
        <div class="g-item" data-aos="fade-in" data-aos-delay="300">
            <img src="https://scontent.fbgw66-2.fna.fbcdn.net/v/t39.30808-6/600433488_1318119280119464_1554093837483668881_n.jpg?stp=dst-jpg_p180x540_tt6&_nc_cat=103&ccb=1-7&_nc_sid=833d8c&_nc_ohc=d8IBIJUSSO4Q7kNvwG-s2hG&_nc_oc=AdmtY9JdWjBbaVFS29Q0lDNa860bHbjPQm9Z5m1VFmyTmIfXLjjldLCnLpIf-SbHMhc&_nc_zt=23&_nc_ht=scontent.fbgw66-2.fna&_nc_gid=6YLyUtbyT8LY1-Zcv4qL7A&oh=00_Afk8Jl6vSSy3yIGzkK6TPDXUyIiRTf_2UJ2IQw_5P8b9Yw&oe=69503639" alt="Passion">
            <div class="g-overlay"><h4 style="color:white; margin:0;">روح الفريق</h4></div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    once: true, // الحركة تعمل مرة واحدة فقط عند النزول
    offset: 100, // يبدأ التأثير قبل وصول العنصر بـ 100 بكسل
  });
</script>

</body>
</html>