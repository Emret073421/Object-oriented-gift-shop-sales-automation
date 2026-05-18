<?php
require_once '../config.php';

$islemKodu = $_POST['islem_kodu'] ?? '';
$islemKodu = $db->real_escape_string(trim($islemKodu));

if (empty($islemKodu)) {
    echo '<div class="alert alert-warning">İşlem kodu bulunamadı.</div>';
    exit;
}

$sql = "SELECT i.miktar, i.birim_fiyat, i.toplam_tutar, u.ad AS urun_adi, u.barkod 
        FROM islemler i 
        LEFT JOIN urunler u ON i.urun_id = u.id 
        WHERE i.islem_kodu = '$islemKodu' AND i.islem_tipi = 'ALIS'";

$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle text-start fs-7">
            <thead class="table-light">
                <tr>
                    <th>Barkod</th>
                    <th>Ürün Adı</th>
                    <th>Birim Alış F.</th>
                    <th>Alınan Miktar</th>
                    <th class="text-end">Ara Toplam</th>
                </tr>
            </thead>
            <tbody>';
    $genelToplam = 0;
    while ($row = $sonuc->fetch_assoc()) {
        $genelToplam += $row['toplam_tutar'];
        echo '<tr>
                <td><span class="badge bg-light text-dark border">' . htmlspecialchars($row['barkod'] ?? '-') . '</span></td>
                <td class="fw-bold">' . htmlspecialchars($row['urun_adi'] ?? 'Silinmiş/Bilinmeyen Ürün') . '</td>
                <td>₺' . number_format($row['birim_fiyat'], 2) . '</td>
                <td><span class="badge bg-primary">' . (int)$row['miktar'] . ' Adet</span></td>
                <td class="text-end fw-bold">₺' . number_format($row['toplam_tutar'], 2) . '</td>
              </tr>';
    }
    echo '</tbody>
          <tfoot class="table-light fw-bold fs-6">
            <tr>
                <td colspan="4" class="text-end">Genel Toplam:</td>
                <td class="text-end text-success">₺' . number_format($genelToplam, 2) . '</td>
            </tr>
          </tfoot>
          </table></div>';
} else {
    echo '<div class="alert alert-danger">Bu koda ait detay bulunamadı.</div>';
}
?>
