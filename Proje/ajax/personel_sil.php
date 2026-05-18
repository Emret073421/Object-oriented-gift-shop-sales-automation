<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Geçersiz personel ID.']);
    exit;
}

$personelManager = new PersonelManager($db);
$sonuc = $personelManager->sil($id);

echo json_encode($sonuc);
?>
