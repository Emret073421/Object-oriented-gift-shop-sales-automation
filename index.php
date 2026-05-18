<?php 
require_once 'config.php';

if(!isset($_SESSION['login'])){
    include 'login.php';
    exit(); // login.php dahil edildikten sonra alttaki kodların çalışmasını kesinlikle durduruyoruz!
}else{  
    $sayfa = $_GET['sayfa'] ?? 'dashboard';
    
    // Header'dan önce sayfa başlığını ($page_title) belirleyelim
    switch ($sayfa) {
        case 'dashboard': $page_title = 'Dashboard (Özet Ekranı)'; break;
        case 'urunler': $page_title = 'Ürün Yönetimi'; break;
        case 'satis': $page_title = 'Hızlı Satış Ekranı (POS)'; break;
        case 'alislar': $page_title = 'Mal Alış (Tedarik) İşlemleri'; break;
        case 'kategoriler': $page_title = 'Kategori Yönetimi'; break;
        case 'personel': $page_title = 'Personel Yönetimi'; break;
        case 'raporlar': $page_title = 'Finans ve Detaylı Raporlar'; break;
        default: $page_title = 'Sayfa Bulunamadı'; break;
    }

    include 'inc/header.php'; 
    
    switch ($sayfa) {
        case 'dashboard':
            include 'dashboard.php';
            break;
        case 'urunler':
            include 'urunler.php';
            break;
        case 'satis':
            include 'satis.php';
            break;
        case 'alislar':
            include 'alislar.php';
            break;
        case 'kategoriler':
            include 'kategoriler.php';
            break;
        case 'personel':
            include 'personel.php';
            break;
        case 'raporlar':
            include 'raporlar.php';
            break;
        default:
            include 'dashboard.php';
            break;
    }
    include 'inc/footer.php';
}
?>
