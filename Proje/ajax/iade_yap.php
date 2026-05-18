<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

$islemKodu = $_POST['islem_kodu'] ?? '';
$urunId = (int)($_POST['urun_id'] ?? 0);
$miktar = (int)($_POST['miktar'] ?? 0);
$aciklama = $_POST['aciklama'] ?? 'Müşteri İadesi';
$personelId = $_SESSION['personel_id'] ?? 1;

if (empty($islemKodu) || $urunId <= 0 || $miktar <= 0) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Lütfen işlem kodunu, ürünü ve iade miktarını eksiksiz girin.']);
    exit;
}

$islemManager = new IslemManager($db);
$sonuc = $islemManager->iadeAl($islemKodu, $urunId, $miktar, $personelId, $aciklama);

echo json_encode($sonuc);
?>
