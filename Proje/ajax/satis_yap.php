<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Sepette ürün bulunamadı.']);
    exit;
}

$not = $data['note'] ?? '';
$personelId = $_SESSION['personel_id'] ?? 1;

$islemManager = new IslemManager($db);
$islemKodu = $islemManager->satisYap($data['items'], $not, $personelId);

if ($islemKodu !== false) {
    echo json_encode(['basarili' => true, 'mesaj' => 'Satış başarıyla tamamlandı!', 'islem_kodu' => $islemKodu]);
} else {
    echo json_encode(['basarili' => false, 'mesaj' => 'Satış işlemi sırasında veritabanı hatası oluştu.']);
}
?>
