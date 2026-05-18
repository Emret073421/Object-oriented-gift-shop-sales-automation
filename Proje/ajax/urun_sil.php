<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Geçersiz ürün ID.']);
    exit;
}

$urunManager = new UrunManager($db);
$sonuc = $urunManager->sil($id);

if ($sonuc) {
    echo json_encode(['basarili' => true, 'mesaj' => 'Ürün başarıyla silindi (arşivlendi).']);
} else {
    echo json_encode(['basarili' => false, 'mesaj' => 'Ürün silinirken bir veritabanı hatası oluştu.']);
}
?>
