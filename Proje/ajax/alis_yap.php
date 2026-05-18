<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$veri = json_decode($input, true);

$tedarikci = trim($veri['tedarikci'] ?? '');
$tarih = trim($veri['tarih'] ?? '');
$not = trim($veri['not'] ?? '');
$sepet = $veri['sepet'] ?? [];

if (empty($sepet)) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Sepetiniz boş. Lütfen ürün ekleyin.']);
    exit;
}

$tedarikci = $db->real_escape_string($tedarikci ?: 'Genel Tedarikçi');
$tarih = $db->real_escape_string($tarih ?: date('Y-m-d H:i:s'));
$not = $db->real_escape_string($not);

// İşlem Kodu Oluşturma: ALIS-YYYYMMDD-RastgeleSayı
$islemKodu = 'ALIS-' . date('Ymd') . '-' . rand(1000, 9999);

$db->begin_transaction();

try {
    foreach ($sepet as $item) {
        $urunId = (int)$item['id'];
        $miktar = (int)$item['qty'];
        $alisFiyati = (float)$item['price'];
        $toplamTutar = $miktar * $alisFiyati;

        if ($urunId <= 0 || $miktar <= 0 || $alisFiyati < 0) {
            throw new Exception("Geçersiz sepet kalemi tespit edildi.");
        }

        // 1. islemler tablosuna ALIS kaydı ekle
        $sqlIslem = "INSERT INTO islemler (islem_kodu, islem_tipi, urun_id, miktar, birim_fiyat, toplam_tutar, musteri_bilgisi, aciklama, islem_tarihi) 
                     VALUES ('$islemKodu', 'ALIS', $urunId, $miktar, $alisFiyati, $toplamTutar, '$tedarikci', '$not', '$tarih')";
        
        if (!$db->query($sqlIslem)) {
            throw new Exception("İşlem kaydı eklenirken hata oluştu: " . $db->error);
        }

        // 2. urunler tablosunda stok_miktari artır ve alis_fiyati güncelle
        $sqlUrun = "UPDATE urunler SET stok_miktari = stok_miktari + $miktar, alis_fiyati = $alisFiyati WHERE id = $urunId";
        if (!$db->query($sqlUrun)) {
            throw new Exception("Stok güncellenirken hata oluştu: " . $db->error);
        }
    }

    $db->commit();
    echo json_encode(['basarili' => true, 'mesaj' => 'Mal alış fişi başarıyla kaydedildi ve stoklar güncellendi!', 'islem_kodu' => $islemKodu]);

} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['basarili' => false, 'mesaj' => $e->getMessage()]);
}
?>
