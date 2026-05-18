<?php
require_once '../config.php';

$arama = $_POST['arama'] ?? '';
$kategori = $_POST['kategori'] ?? 'tumu';

$arama = $db->real_escape_string($arama);
$kategori = $db->real_escape_string($kategori);

$sql = "SELECT u.*, k.ad AS kategori_adi FROM urunler u LEFT JOIN kategoriler k ON u.kategori_id = k.id WHERE u.durum = 1";

if ($kategori !== 'tumu') {
    $sql .= " AND u.kategori_id = " . (int)$kategori;
}

if ($arama !== '') {
    $sql .= " AND (u.ad LIKE '%$arama%' OR u.barkod LIKE '%$arama%' OR k.ad LIKE '%$arama%')";
}

$sql .= " ORDER BY u.ad ASC";

$sonuc = $db->query($sql);

if ($sonuc && $sonuc->num_rows > 0) {
    while ($urun = $sonuc->fetch_assoc()) {
        $stok = (int)$urun['stok_miktari'];
        $stokRenk = ($stok > 15) ? 'text-success' : 'text-danger';
        $stokIkon = ($stok > 15) ? 'fa-check' : 'fa-triangle-exclamation';
        
        // JS addSaleItem fonksiyonuna gönderilecek JSON objesi
        $urunJson = htmlspecialchars(json_encode([
            'id' => (int)$urun['id'],
            'ad' => $urun['ad'],
            'fiyat' => (float)$urun['satis_fiyati'],
            'stok' => $stok,
            'barkod' => $urun['barkod']
        ]), ENT_QUOTES, 'UTF-8');

        echo '
        <div class="col">
            <div class="pos-product-card p-3 h-100 d-flex flex-column" onclick="addSaleItem(' . $urunJson . ')">
                <h6 class="fw-bold mb-1 text-white">' . htmlspecialchars($urun['ad']) . '</h6>
                <small class="text-muted d-block mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">' . htmlspecialchars($urun['barkod'] ?? 'Barkodsuz') . '</small>
                <small class="d-block mb-auto text-secondary">' . htmlspecialchars($urun['kategori_adi'] ?? 'Genel') . '</small>
                <div class="mt-3 d-flex justify-content-between align-items-end">
                    <div class="pos-price mb-0">₺' . number_format($urun['satis_fiyati'], 2) . '</div>
                    <div class="pos-stock ' . $stokRenk . '"><i class="fa-solid ' . $stokIkon . ' me-1"></i> ' . $stok . ' adet</div>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<div class="col-12 py-4 text-center text-muted"><i class="fa-solid fa-box-open fa-2x mb-2"></i><p>Arama kriterine uygun ürün bulunamadı.</p></div>';
}
?>
