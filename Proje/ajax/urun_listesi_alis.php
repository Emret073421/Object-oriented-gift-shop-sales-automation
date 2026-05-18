<?php
require_once '../config.php';

$sql = "SELECT u.id, u.barkod, u.ad, u.alis_fiyati, u.stok_miktari, k.ad AS kategori_adi 
        FROM urunler u 
        LEFT JOIN kategoriler k ON u.kategori_id = k.id 
        WHERE u.durum = 1 ORDER BY u.ad ASC";

$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle text-start fs-7">
            <thead class="table-light">
                <tr>
                    <th>Barkod</th>
                    <th>Ürün Adı</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Alış F.</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = $sonuc->fetch_assoc()) {
        $urunJson = htmlspecialchars(json_encode([
            'id' => (int)$row['id'],
            'barkod' => $row['barkod'],
            'ad' => $row['ad'],
            'alis_fiyati' => (float)$row['alis_fiyati']
        ]), ENT_QUOTES, 'UTF-8');

        echo '<tr>
                <td><span class="badge bg-light text-dark border">' . htmlspecialchars($row['barkod']) . '</span></td>
                <td class="fw-bold">' . htmlspecialchars($row['ad']) . '</td>
                <td><span class="badge bg-secondary">' . htmlspecialchars($row['kategori_adi'] ?? 'Genel') . '</span></td>
                <td>' . (int)$row['stok_miktari'] . '</td>
                <td>₺' . number_format($row['alis_fiyati'], 2) . '</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-primary" onclick="secilenUrunuSepeteEkle(' . $urunJson . ')">
                        <i class="fa-solid fa-plus me-1"></i> Seç
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-warning text-center py-4 mb-0"><i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>Sistemde kayıtlı aktif ürün bulunamadı.</div>';
}
?>
