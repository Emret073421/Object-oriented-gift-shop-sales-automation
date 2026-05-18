<?php
require_once '../config.php';

$islemKodu = $_POST['islem_kodu'] ?? '';
$islemKodu = $db->real_escape_string(trim($islemKodu));

if (empty($islemKodu)) {
    echo '<div class="alert alert-warning">Lütfen bir işlem kodu girin.</div>';
    exit;
}

// SATIS ve IADE kayıtlarını gruplayarak kalan iade miktarını hesaplıyoruz
$sql = "SELECT i.urun_id, u.ad AS urun_adi, u.barkod, i.birim_fiyat, i.islem_kodu, MAX(i.islem_tarihi) AS islem_tarihi,
               SUM(CASE WHEN i.islem_tipi = 'SATIS' THEN i.miktar ELSE 0 END) AS satis_miktari,
               SUM(CASE WHEN i.islem_tipi = 'IADE' THEN i.miktar ELSE 0 END) AS iade_miktari
        FROM islemler i 
        INNER JOIN urunler u ON i.urun_id = u.id 
        WHERE i.islem_kodu = '$islemKodu' AND i.islem_tipi IN ('SATIS', 'IADE')
        GROUP BY i.urun_id, u.ad, u.barkod, i.birim_fiyat, i.islem_kodu";

$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle bg-white shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th>İşlem Kodu</th>
                    <th>Barkod</th>
                    <th>Ürün Adı</th>
                    <th>Birim Fiyat</th>
                    <th>Satış / İade Durumu</th>
                    <th>Kalan İade Hakkı</th>
                    <th>Tarih</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = $sonuc->fetch_assoc()) {
        $urunId = (int)$row['urun_id'];
        $satisMiktari = (int)$row['satis_miktari'];
        $iadeMiktari = (int)$row['iade_miktari'];
        $kalanIade = $satisMiktari - $iadeMiktari;

        $urunAdi = htmlspecialchars($row['urun_adi']);
        $islemKoduJs = htmlspecialchars($row['islem_kodu']);

        // İade durumu gösterimi
        if ($iadeMiktari === 0) {
            $durumBadge = '<span class="badge bg-success fs-7">Tamamı Satışta (' . $satisMiktari . ')</span>';
        } elseif ($kalanIade > 0) {
            $durumBadge = '<span class="badge bg-warning text-dark fs-7">Kısmi İade (' . $iadeMiktari . '/' . $satisMiktari . ')</span>';
        } else {
            $durumBadge = '<span class="badge bg-danger fs-7">Tamamı İade Edildi (' . $satisMiktari . ')</span>';
        }

        echo '<tr>
                <td><span class="badge bg-secondary">' . $islemKoduJs . '</span></td>
                <td>' . htmlspecialchars($row['barkod'] ?? '-') . '</td>
                <td class="fw-bold">' . $urunAdi . '</td>
                <td>₺' . number_format($row['birim_fiyat'], 2) . '</td>
                <td>' . $durumBadge . '</td>
                <td><span class="badge bg-info text-dark fs-6">' . $kalanIade . ' Adet</span></td>
                <td>' . date('d.m.Y H:i', strtotime($row['islem_tarihi'])) . '</td>
                <td class="text-end">';

        if ($kalanIade > 0) {
            echo '<button class="btn btn-sm btn-danger" onclick="iadePenceresi(\'' . $islemKoduJs . '\', ' . $urunId . ', \'' . addslashes($urunAdi) . '\', ' . $kalanIade . ')">
                    <i class="fa-solid fa-rotate-left me-1"></i> İade Al
                  </button>';
        } else {
            echo '<button class="btn btn-sm btn-outline-secondary" disabled><i class="fa-solid fa-check me-1"></i> İade Edildi</button>';
        }

        echo '  </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i> <b>' . htmlspecialchars($islemKodu) . '</b> koduna ait geçerli bir satış/iade kaydı bulunamadı.</div>';
}
?>
