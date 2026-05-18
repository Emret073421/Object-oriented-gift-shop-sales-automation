<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);
$ad = trim($_POST['ad'] ?? '');
$aciklama = trim($_POST['aciklama'] ?? '');

if ($id <= 0 || empty($ad)) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen kategori adını eksiksiz girin.']);
    exit;
}

// Çakışma kontrolü (Kendi ID'si hariç)
$kontrol = $db->query("SELECT id FROM kategoriler WHERE ad = '$ad' AND id != $id AND durum = 1");
if ($kontrol && $kontrol->num_rows > 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Bu isimde başka bir aktif kategori zaten mevcut.']);
    exit;
}

$kategoriManager = new KategoriManager($db);
$sonuc = $kategoriManager->kategoriGuncelle($id, $ad, $aciklama);

if ($sonuc) {
    echo json_encode(['basarili' => true, 'mesaj' => 'Kategori başarıyla güncellendi.']);
} else {
    echo json_encode(['basarili' => false, 'mesaj' => 'Kategori güncellenirken veritabanı hatası oluştu.']);
}
?>
