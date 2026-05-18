<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$ad = trim($_POST['ad'] ?? '');
$aciklama = trim($_POST['aciklama'] ?? '');

if (empty($ad)) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen kategori adını girin.']);
    exit;
}

// %100 OOP: Kategori model nesnemizi üretip setterlar ile dolduruyoruz
$kategori = new Kategori();
$kategori->setAd($ad);
$kategori->setAciklama($aciklama);

// KategoriManager nesnesini çağırıp, Model nesnemizi teslim ediyoruz
$kategoriManager = new KategoriManager($db);
$sonuc = $kategoriManager->kategoriEkle($kategori);

echo json_encode($sonuc);
?>
