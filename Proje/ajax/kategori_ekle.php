<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$ad = trim($_POST['ad'] ?? '');
$aciklama = trim($_POST['aciklama'] ?? '');

if (empty($ad)) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen kategori adını girin.']);
    exit;
}

// Çakışma kontrolü
$kontrol = $db->query("SELECT id FROM kategoriler WHERE ad = '$ad' AND durum = 1");
if ($kontrol && $kontrol->num_rows > 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Bu isimde aktif bir kategori zaten mevcut.']);
    exit;
}

$kategoriManager = new KategoriManager($db);
$sonuc = $kategoriManager->kategoriEkle($ad, $aciklama);

if ($sonuc) {
    echo json_encode(['basarili' => true, 'mesaj' => 'Kategori başarıyla eklendi.']);
} else {
    echo json_encode(['basarili' => false, 'mesaj' => 'Kategori eklenirken veritabanı hatası oluştu.']);
}
?>
