<?php
require_once '../config.php';

$baslangic = $_POST['baslangic'] ?? '';
$bitis = $_POST['bitis'] ?? '';

$where = "WHERE i.islem_tipi = 'ALIS'";

if (!empty($baslangic) && !empty($bitis)) {
    $baslangic = $db->real_escape_string($baslangic . ' 00:00:00');
    $bitis = $db->real_escape_string($bitis . ' 23:59:59');
    $where .= " AND i.islem_tarihi BETWEEN '$baslangic' AND '$bitis'";
}

$sql = "SELECT i.islem_kodu, MAX(i.islem_tarihi) AS islem_tarihi, MAX(i.musteri_bilgisi) AS tedarikci, MAX(i.aciklama) AS notlar,
               COUNT(i.id) AS cesit_sayisi, SUM(i.miktar) AS toplam_miktar, SUM(i.toplam_tutar) AS genel_toplam
        FROM islemler i
        $where
        GROUP BY i.islem_kodu
        ORDER BY islem_tarihi DESC";

$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle bg-white shadow-sm rounded border mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">İşlem Kodu</th>
                    <th>Tarih</th>
                    <th>Tedarikçi / Not</th>
                    <th>Çeşit / Miktar</th>
                    <th>Toplam Tutar</th>
                    <th class="pe-4 text-end">Detay</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = $sonuc->fetch_assoc()) {
        $islemKodu = htmlspecialchars($row['islem_kodu']);
        $tedarikci = htmlspecialchars($row['tedarikci'] ?: 'Genel Tedarikçi');
        $notlar = htmlspecialchars($row['notlar'] ?: '-');

        echo '<tr>
                <td class="ps-4"><span class="badge bg-secondary fs-7">' . $islemKodu . '</span></td>
                <td>' . date('d.m.Y H:i', strtotime($row['islem_tarihi'])) . '</td>
                <td>
                    <div class="fw-bold text-dark">' . $tedarikci . '</div>
                    <div class="text-muted fs-7">' . $notlar . '</div>
                </td>
                <td><span class="badge bg-info text-dark fs-7">' . $row['cesit_sayisi'] . ' Çeşit / ' . $row['toplam_miktar'] . ' Adet</span></td>
                <td class="fw-bold text-primary">₺' . number_format($row['genel_toplam'], 2) . '</td>
                <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-outline-primary" onclick="alisDetayGoster(\'' . $islemKodu . '\')">
                        <i class="fa-solid fa-eye me-1"></i> İncele
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-warning text-center py-4 mb-0"><i class="fa-solid fa-file-invoice fa-2x mb-2 d-block"></i>Belirtilen kriterlerde mal alış kaydı bulunamadı.</div>';
}
?>
