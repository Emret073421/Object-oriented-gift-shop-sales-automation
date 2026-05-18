<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Geçersiz kategori ID.']);
    exit;
}

$kategoriManager = new KategoriManager($db);
$sonuc = $kategoriManager->sil($id);

echo json_encode($sonuc);
?>
