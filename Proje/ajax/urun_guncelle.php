<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);
$barkod = trim($_POST['barkod'] ?? '');
$ad = trim($_POST['ad'] ?? '');
$kategori_id = (int)($_POST['kategori_id'] ?? 1);
$alis_fiyati = (float)($_POST['alis_fiyati'] ?? 0);
$satis_fiyati = (float)($_POST['satis_fiyati'] ?? 0);
$stok_miktari = (int)($_POST['stok_miktari'] ?? 0);

if ($id <= 0 || empty($barkod) || empty($ad) || $alis_fiyati <= 0 || $satis_fiyati <= 0 || $stok_miktari < 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen tüm alanları eksiksiz ve geçerli değerlerle doldurun.']);
    exit;
}

// %100 OOP: Değişkenleri doğrudan göndermek yerine Urun model nesnemizi üretiyoruz
$urun = new Urun();
$urun->setId($id);
$urun->setBarkod($barkod);
$urun->setAd($ad);
$urun->setAlisFiyati($alis_fiyati);
$urun->setSatisFiyati($satis_fiyati);
$urun->setStokMiktari($stok_miktari);
$urun->setKategoriId($kategori_id);

// Yönetici (Manager) nesnemizi çağırıp, Model nesnesini (Urun) teslim ediyoruz
$urunManager = new UrunManager($db);
$sonuc = $urunManager->urunGuncelle($urun);

echo json_encode($sonuc);
?>
