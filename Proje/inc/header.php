<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hediyelik Otomasyonu - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome (İkonlar için) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; }
        #wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        #sidebar { width: 260px; background-color: #2c3e50; color: #fff; display: flex; flex-direction: column; transition: all 0.3s; box-shadow: 2px 0 5px rgba(0,0,0,0.1); z-index: 10; }
        #sidebar .sidebar-header { padding: 25px 20px; background-color: #1a252f; text-align: center; font-size: 1.4rem; font-weight: 600; letter-spacing: 1px; }
        #sidebar ul.components { padding: 15px 0; flex-grow: 1; overflow-y: auto; }
        #sidebar ul li a { padding: 15px 25px; font-size: 1.05em; display: flex; align-items: center; color: #bdc3c7; text-decoration: none; transition: 0.2s; }
        #sidebar ul li a:hover, #sidebar ul li.active > a { color: #fff; background: #34495e; border-left: 5px solid #3498db; }
        #sidebar ul li a i { margin-right: 15px; width: 20px; text-align: center; font-size: 1.1em; }
        #content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .navbar-custom { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 30px; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: transform 0.2s ease-in-out; }
        .card-custom:hover { transform: translateY(-5px); }
        .stat-icon { opacity: 0.2; position: absolute; right: 20px; top: 20px; font-size: 4rem; }
        .card-body { position: relative; overflow: hidden; }
    </style>
</head>
<body>
<?php
// Hangi sayfada olduğumuzu $_GET['sayfa'] parametresinden buluyoruz
$current_page = $_GET['sayfa'] ?? 'dashboard';
?>
<div id="wrapper">
    <!-- SOL MENÜ (SIDEBAR) -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-gift text-info me-2"></i>HediyeOto
        </div>
        <ul class="list-unstyled components">
            <li class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>">
                <a href="index.php?sayfa=dashboard"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            </li>
            <li class="<?= ($current_page == 'satis') ? 'active' : '' ?>">
                <a href="index.php?sayfa=satis"><i class="fa-solid fa-cart-arrow-down"></i> Hızlı Satış / Değişim</a>
            </li>
            <li class="<?= ($current_page == 'urunler') ? 'active' : '' ?>">
                <a href="index.php?sayfa=urunler"><i class="fa-solid fa-boxes-stacked"></i> Ürün Yönetimi</a>
            </li>
            <li class="<?= ($current_page == 'kategoriler') ? 'active' : '' ?>">
                <a href="index.php?sayfa=kategoriler"><i class="fa-solid fa-tags"></i> Kategoriler</a>
            </li>
            <li class="<?= ($current_page == 'alislar') ? 'active' : '' ?>">
                <a href="index.php?sayfa=alislar"><i class="fa-solid fa-truck-ramp-box"></i> Mal Alış (Tedarik)</a>
            </li>
            <li class="<?= ($current_page == 'raporlar') ? 'active' : '' ?>">
                <a href="index.php?sayfa=raporlar"><i class="fa-solid fa-file-invoice-dollar"></i> Finans ve Raporlar</a>
            </li>
            <li class="<?= ($current_page == 'personel') ? 'active' : '' ?>">
                <a href="index.php?sayfa=personel"><i class="fa-solid fa-users-gear"></i> Personel Yönetimi</a>
            </li>
        </ul>
        <div class="p-4 border-top border-secondary">
            <div class="small text-muted mb-2">Giriş Yapan:</div>
            <div class="d-flex align-items-center mb-3">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['personel_adi'] ?? 'Misafir'); ?>&background=3498db&color=fff&rounded=true" width="40" class="me-2">
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['personel_adi'] ?? 'Misafir'); ?></strong><br>
                    <small class="text-info"><?php echo ucfirst(strtolower($_SESSION['personel_yetki'] ?? 'Personel')); ?></small>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger w-100 btn-sm" onclick="logout()">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Çıkış Yap
            </button>
        </div>
    </nav>

    <!-- SAĞ İÇERİK (CONTENT) -->
    <div id="content">
        <!-- ÜST BİLGİ ÇUBUĞU (NAVBAR) -->
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <div class="container-fluid">
                <h5 class="mb-0 text-dark fw-bold"><?= isset($page_title) ? $page_title : 'Sayfa' ?></h5>
                
                <!-- Sağ Üst Araçlar -->
                <div class="d-flex align-items-center">
                    <a href="index.php?sayfa=satis" class="btn btn-primary me-3 shadow-sm rounded-pill px-4">
                        <i class="fa-solid fa-plus me-2"></i> Yeni İşlem
                    </a>
                    <span class="text-muted"><i class="fa-regular fa-calendar me-2"></i> <?= date('d.m.Y') ?></span>
                </div>
            </div>
        </nav>

        <!-- ANA İÇERİK ALANI (Tüm sayfalara buraya içerik gelecek) -->
        <div class="container-fluid p-4">

         <script>
            function logout() {
                window.location.href = "logout.php";
            }
         </script>