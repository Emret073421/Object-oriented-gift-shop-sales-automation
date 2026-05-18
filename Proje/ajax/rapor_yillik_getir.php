<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$yil = (int)($_POST['yil'] ?? date('Y'));

if ($yil < 2000) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Geçersiz yıl parametresi.']);
    exit;
}

// 1. Yıllık Toplam Hasılat (SATIS - IADE)
$sqlYillik = "SELECT 
                SUM(CASE WHEN islem_tipi = 'SATIS' THEN toplam_tutar ELSE 0 END) AS satis_toplam,
                SUM(CASE WHEN islem_tipi = 'IADE' THEN ABS(toplam_tutar) ELSE 0 END) AS iade_toplam
              FROM islemler 
              WHERE YEAR(islem_tarihi) = $yil AND islem_tipi IN ('SATIS', 'IADE')";

$qYillik = $db->query($sqlYillik);
$yillik = $qYillik ? $qYillik->fetch_assoc() : ['satis_toplam' => 0, 'iade_toplam' => 0];
$netYillikCiro = (float)$yillik['satis_toplam'] - (float)$yillik['iade_toplam'];

// 2. Yılın En Çok Satılan Ürünü
$sqlEnCok = "SELECT u.ad, SUM(i.miktar) AS toplam_satilan 
             FROM islemler i 
             INNER JOIN urunler u ON i.urun_id = u.id 
             WHERE YEAR(i.islem_tarihi) = $yil AND i.islem_tipi = 'SATIS' 
             GROUP BY i.urun_id, u.ad 
             ORDER BY toplam_satilan DESC LIMIT 1";

$qEnCok = $db->query($sqlEnCok);
if ($qEnCok && $qEnCok->num_rows > 0) {
    $enCok = $qEnCok->fetch_assoc();
    $enCokSatanMetin = htmlspecialchars($enCok['ad']) . ' (' . (int)$enCok['toplam_satilan'] . ' Adet)';
} else {
    $enCokSatanMetin = 'Bu yıla ait satış kaydı yok.';
}

// 3. 12 Ayın Dağılımını Hesapla
$aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
$tabloHtml = '';

for ($i = 1; $i <= 12; $i++) {
    $ayStr = str_pad($i, 2, '0', STR_PAD_LEFT);
    $baslangic = "$yil-$ayStr-01 00:00:00";
    $bitis = date("Y-m-t 23:59:59", strtotime($baslangic));

    $sqlAy = "SELECT 
                COUNT(DISTINCT CASE WHEN islem_tipi = 'SATIS' THEN islem_kodu ELSE NULL END) AS siparis,
                SUM(CASE WHEN islem_tipi = 'SATIS' THEN miktar ELSE 0 END) AS urun_adet,
                SUM(CASE WHEN islem_tipi = 'SATIS' THEN toplam_tutar ELSE 0 END) - SUM(CASE WHEN islem_tipi = 'IADE' THEN ABS(toplam_tutar) ELSE 0 END) AS ay_ciro
              FROM islemler 
              WHERE islem_tarihi BETWEEN '$baslangic' AND '$bitis' AND islem_tipi IN ('SATIS', 'IADE')";

    $qAy = $db->query($sqlAy);
    $ayVeri = $qAy ? $qAy->fetch_assoc() : ['siparis' => 0, 'urun_adet' => 0, 'ay_ciro' => 0];

    $siparis = (int)$ayVeri['siparis'];
    $urunAdet = (int)$ayVeri['urun_adet'];
    $ayCiro = (float)$ayVeri['ay_ciro'];

    if ($siparis > 0 || $urunAdet > 0 || $ayCiro != 0) {
        $ciroGosterim = '<span class="fw-bold text-success">₺' . number_format($ayCiro, 2) . '</span>';
        $satirSinif = 'fw-bold bg-light';
    } else {
        $ciroGosterim = '<span class="text-muted">-</span>';
        $satirSinif = '';
    }

    $tabloHtml .= '<tr class="' . $satirSinif . '">
                    <td class="ps-4">' . $aylar[$i - 1] . '</td>
                    <td class="text-center">' . ($siparis > 0 ? $siparis : '-') . '</td>
                    <td class="text-center">' . ($urunAdet > 0 ? $urunAdet : '-') . '</td>
                    <td class="pe-4 text-end">' . $ciroGosterim . '</td>
                   </tr>';
}

echo json_encode([
    'basarili' => true,
    'yillik_ciro' => number_format($netYillikCiro, 2, ',', '.') . ' ₺',
    'en_cok_satan' => $enCokSatanMetin,
    'tablo_html' => $tabloHtml
]);
?>
