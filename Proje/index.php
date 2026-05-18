<?php 
require_once 'config.php';

if(!isset($_SESSION['login'])){
    include 'login.php';
    exit(); 
}else{  
    $sayfa = $_GET['sayfa'] ?? 'dashboard';
    $yetki = strtoupper($_SESSION['personel_yetki'] ?? 'PERSONEL');

    // 1. Yetki Kontrol Tablosu (Kasiyer: dashboard, satis | Personel: dashboard, satis, alislar | Yonetici: hepsi)
    $izinli = false;
    if ($yetki === 'YONETICI') {
        $izinli = true;
    } elseif ($yetki === 'PERSONEL' && in_array($sayfa, ['dashboard', 'satis', 'alislar'])) {
        $izinli = true;
    } elseif ($yetki === 'KASIYER' && in_array($sayfa, ['dashboard', 'satis'])) {
        $izinli = true;
    }

    // 2. Anlık İzin Kontrolü (SweetAlert2 üzerinden geçici izin alınmışsa)
    if (isset($_SESSION['anlik_izin_sayfa']) && $_SESSION['anlik_izin_sayfa'] === $sayfa) {
        $izinli = true;
        // İzni kullandıktan sonra tek seferlik olması için silebiliriz
        unset($_SESSION['anlik_izin_sayfa']);
    }

    // 3. İzin Yoksa Yetkisiz Erişim Sayfası Göster
    if (!$izinli) {
        $page_title = 'Yetkisiz Erişim';
        include 'inc/header.php';
        echo '<div class="alert alert-danger m-5 p-5 text-center shadow-sm rounded-4 bg-white border border-danger">
                <i class="fa-solid fa-user-lock fa-4x mb-3 text-danger"></i>
                <h3 class="fw-bold text-dark">Erişim Engellendi</h3>
                <p class="text-muted mb-4">Bu sayfayı görüntülemek için mevcut yetki seviyeniz (<b>' . htmlspecialchars($yetki) . '</b>) yetersizdir.<br>Sayfaya erişmek için sol menüden ilgili butona tıklayarak anlık yönetici girişi yapabilirsiniz.</p>
                <a href="index.php?sayfa=satis" class="btn btn-primary px-4 py-2 fw-bold"><i class="fa-solid fa-cart-arrow-down me-2"></i> Satış Ekranına Git</a>
              </div>';
        include 'inc/footer.php';
        exit();
    }

    // Header'dan önce sayfa başlığını ($page_title) belirleyelim
    switch ($sayfa) {
        case 'dashboard': $page_title = 'Dashboard (Özet Ekranı)'; break;
        case 'urunler': $page_title = 'Ürün Yönetimi'; break;
        case 'satis': $page_title = 'Hızlı Satış Ekranı (POS)'; break;
        case 'alislar': $page_title = 'Mal Alış (Tedarik) İşlemleri'; break;
        case 'kategoriler': $page_title = 'Kategori Yönetimi'; break;
        case 'personel': $page_title = 'Personel Yönetimi'; break;
        case 'raporlar': $page_title = 'Finans ve Detaylı Raporlar'; break;
        case 'iade': $page_title = 'Ürün İade ve İptal İşlemleri'; break;
        default: $page_title = 'Sayfa Bulunamadı'; break;
    }

    include 'inc/header.php'; 
    
    switch ($sayfa) {
        case 'dashboard': include 'dashboard.php'; break;
        case 'urunler': include 'urunler.php'; break;
        case 'satis': include 'satis.php'; break;
        case 'alislar': include 'alislar.php'; break;
        case 'kategoriler': include 'kategoriler.php'; break;
        case 'personel': include 'personel.php'; break;
        case 'raporlar': include 'raporlar.php'; break;
        case 'iade': include 'iade.php'; break;
        default: include 'dashboard.php'; break;
    }
    include 'inc/footer.php';
}
?>
