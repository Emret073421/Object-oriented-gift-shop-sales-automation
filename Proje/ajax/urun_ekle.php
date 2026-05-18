<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$barkod = trim($_POST['barkod'] ?? '');
$ad = trim($_POST['ad'] ?? '');
$kategori_id = (int)($_POST['kategori_id'] ?? 1);
$alis_fiyati = (float)($_POST['alis_fiyati'] ?? 0);
$satis_fiyati = (float)($_POST['satis_fiyati'] ?? 0);
$stok_miktari = (int)($_POST['stok_miktari'] ?? 0);

if (empty($barkod) || empty($ad) || $alis_fiyati <= 0 || $satis_fiyati <= 0 || $stok_miktari < 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen tüm alanları eksiksiz ve geçerli değerlerle doldurun.']);
    exit;
}

// Barkod kontrolü
$kontrol = $db->query("SELECT id FROM urunler WHERE barkod = '$barkod' AND durum = 1");
if ($kontrol && $kontrol->num_rows > 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Bu barkoda sahip aktif bir ürün zaten sistemde mevcut.']);
    exit;
}

$urunManager = new UrunManager($db);
$sonuc = $urunManager->urunEkle($barkod, $ad, $alis_fiyati, $satis_fiyati, $stok_miktari, $kategori_id);

if ($sonuc) {
    echo json_encode(['basarili' => true, 'mesaj' => 'Ürün başarıyla eklendi.']);
} else {
    echo json_encode(['basarili' => false, 'mesaj' => 'Ürün eklenirken bir veritabanı hatası oluştu.']);
}
?>
