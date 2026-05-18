<?php
require_once '../config.php';
header('Content-Type: application/json; charset=utf-8');

$ay = (int)($_POST['ay'] ?? date('n'));
$yil = (int)($_POST['yil'] ?? date('Y'));

if ($ay < 1 || $ay > 12 || $yil < 2000) {
    echo json_encode(['basarili' => false, 'mesaj' => 'Geçersiz tarih parametreleri.']);
    exit;
}

$ayStr = str_pad($ay, 2, '0', STR_PAD_LEFT);
$baslangic = "$yil-$ayStr-01 00:00:00";
$bitis = date("Y-m-t 23:59:59", strtotime($baslangic));

// 1. Aylık İstatistikleri Hesapla
$sqlOzet = "SELECT 
                SUM(CASE WHEN islem_tipi = 'SATIS' THEN toplam_tutar ELSE 0 END) AS satis_toplam,
                SUM(CASE WHEN islem_tipi = 'IADE' THEN ABS(toplam_tutar) ELSE 0 END) AS iade_toplam,
                SUM(CASE WHEN islem_tipi = 'SATIS' THEN miktar ELSE 0 END) AS satilan_adet,
                COUNT(DISTINCT CASE WHEN islem_tipi = 'SATIS' THEN islem_kodu ELSE NULL END) AS siparis_sayisi
            FROM islemler 
            WHERE islem_tarihi BETWEEN '$baslangic' AND '$bitis' AND islem_tipi IN ('SATIS', 'IADE')";

$qOzet = $db->query($sqlOzet);
$ozet = $qOzet ? $qOzet->fetch_assoc() : ['satis_toplam' => 0, 'iade_toplam' => 0, 'satilan_adet' => 0, 'siparis_sayisi' => 0];

$netCiro = (float)$ozet['satis_toplam'] - (float)$ozet['iade_toplam'];
$satilanAdet = (int)$ozet['satilan_adet'];
$siparisAdet = (int)$ozet['siparis_sayisi'];

// 2. Tablo Satırlarını Oluştur
$sqlTablo = "SELECT i.islem_kodu, i.islem_tarihi, i.miktar, i.birim_fiyat, i.toplam_tutar, i.islem_tipi, u.ad AS urun_adi 
             FROM islemler i 
             LEFT JOIN urunler u ON i.urun_id = u.id 
             WHERE i.islem_tarihi BETWEEN '$baslangic' AND '$bitis' AND i.islem_tipi IN ('SATIS', 'IADE')
             ORDER BY i.islem_tarihi DESC";

$qTablo = $db->query($sqlTablo);
$tabloHtml = '';

if ($qTablo && $qTablo->num_rows > 0) {
    while ($row = $qTablo->fetch_assoc()) {
        $tipBadge = $row['islem_tipi'] === 'SATIS' ? '<span class="badge bg-success fs-7">Satış</span>' : '<span class="badge bg-danger fs-7">İade</span>';
        $tutarRenk = $row['islem_tipi'] === 'SATIS' ? 'text-success' : 'text-danger';
        $isaret = $row['islem_tipi'] === 'SATIS' ? '' : '-';

        $tabloHtml .= '<tr>
                        <td>
                            <div class="fw-bold text-dark">' . date('d.m.Y H:i', strtotime($row['islem_tarihi'])) . '</div>
                            <div class="text-muted fs-7">' . htmlspecialchars($row['islem_kodu']) . '</div>
                        </td>
                        <td>
                            <div class="fw-bold">' . htmlspecialchars($row['urun_adi'] ?? 'Silinmiş/Bilinmeyen Ürün') . '</div>
                            <div>' . $tipBadge . '</div>
                        </td>
                        <td class="text-center fw-bold">' . (int)$row['miktar'] . '</td>
                        <td class="text-end">₺' . number_format($row['birim_fiyat'], 2) . '</td>
                        <td class="text-end fw-bold ' . $tutarRenk . '">' . $isaret . '₺' . number_format($row['toplam_tutar'], 2) . '</td>
                       </tr>';
    }
} else {
    $tabloHtml = '<tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-receipt fa-2x mb-2 d-block"></i>Bu aya ait satış/iade hareketi bulunamadı.</td></tr>';
}

echo json_encode([
    'basarili' => true,
    'net_ciro' => number_format($netCiro, 2, ',', '.') . ' ₺',
    'satilan_adet' => $satilanAdet . ' Adet',
    'siparis_adet' => $siparisAdet . ' Adet',
    'tablo_html' => $tabloHtml
]);
?>
