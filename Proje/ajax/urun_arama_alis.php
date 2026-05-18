<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$barkod = trim($_POST['barkod'] ?? '');
$barkod = $db->real_escape_string($barkod);

if (empty($barkod)) {
    echo json_encode(['bulundu' => false, 'mesaj' => 'Barkod boş olamaz.']);
    exit;
}

// Barkod veya isme göre arama yapalım
$sql = "SELECT id, barkod, ad, alis_fiyati FROM urunler WHERE (barkod = '$barkod' OR ad LIKE '%$barkod%') AND durum = 1 LIMIT 1";
$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    $urun = $sonuc->fetch_assoc();
    echo json_encode([
        'bulundu' => true,
        'urun' => [
            'id' => (int)$urun['id'],
            'barkod' => $urun['barkod'],
            'ad' => $urun['ad'],
            'alis_fiyati' => (float)$urun['alis_fiyati']
        ]
    ]);
} else {
    echo json_encode(['bulundu' => false, 'mesaj' => 'Bu barkoda veya isme sahip aktif bir ürün bulunamadı.']);
}
?>
