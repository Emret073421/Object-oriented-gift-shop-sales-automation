<?php
require_once '../config.php';

$islemKodu = $_POST['islem_kodu'] ?? '';
$islemKodu = $db->real_escape_string(trim($islemKodu));

if (empty($islemKodu)) {
    echo '<div class="alert alert-warning">Lütfen bir işlem kodu girin.</div>';
    exit;
}

$sql = "SELECT i.*, u.ad AS urun_adi, u.barkod 
        FROM islemler i 
        INNER JOIN urunler u ON i.urun_id = u.id 
        WHERE i.islem_kodu = '$islemKodu' AND i.islem_tipi = 'SATIS'";

$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle bg-white shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th>İşlem Kodu</th>
                    <th>Barkod</th>
                    <th>Ürün Adı</th>
                    <th>Birim Fiyat</th>
                    <th>Satış Miktarı</th>
                    <th>Toplam Tutar</th>
                    <th>Tarih</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = $sonuc->fetch_assoc()) {
        $urunId = (int)$row['urun_id'];
        $miktar = (int)$row['miktar'];
        $urunAdi = htmlspecialchars($row['urun_adi']);
        $islemKoduJs = htmlspecialchars($row['islem_kodu']);

        echo '<tr>
                <td><span class="badge bg-secondary">' . $islemKoduJs . '</span></td>
                <td>' . htmlspecialchars($row['barkod'] ?? '-') . '</td>
                <td class="fw-bold">' . $urunAdi . '</td>
                <td>₺' . number_format($row['birim_fiyat'], 2) . '</td>
                <td><span class="badge bg-info text-dark fs-6">' . $miktar . ' Adet</span></td>
                <td class="text-success fw-bold">₺' . number_format($row['toplam_tutar'], 2) . '</td>
                <td>' . date('d.m.Y H:i', strtotime($row['islem_tarihi'])) . '</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-danger" onclick="iadePenceresi(\'' . $islemKoduJs . '\', ' . $urunId . ', \'' . addslashes($urunAdi) . '\', ' . $miktar . ')">
                        <i class="fa-solid fa-rotate-left me-1"></i> İade Al
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i> <b>' . htmlspecialchars($islemKodu) . '</b> koduna ait geçerli bir satış kaydı bulunamadı.</div>';
}
?>
