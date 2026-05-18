<?php
require_once '../config.php';

$arama = $_POST['arama'] ?? '';
$kategori = $_POST['kategori'] ?? 'tumu';

$urunManager = new UrunManager($db);
$urunler = $urunManager->getir($kategori, $arama);

if ($urunler && count($urunler) > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle bg-white shadow-sm rounded border">
            <thead class="table-light">
                <tr>
                    <th>Barkod</th>
                    <th>Ürün Adı</th>
                    <th>Kategori</th>
                    <th>Alış Fiyatı</th>
                    <th>Satış Fiyatı</th>
                    <th>Stok Miktarı</th>
                    <th class="text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($urunler as $urun) {
        $stok = $urun->getStokMiktari();
        $stokBadge = ($stok > 15) ? '<span class="badge bg-success fs-7">' . $stok . ' Adet</span>' : '<span class="badge bg-warning text-dark fs-7">' . $stok . ' Adet</span>';
        
        $urunJson = htmlspecialchars(json_encode([
            'id' => $urun->getId(),
            'barkod' => $urun->getBarkod(),
            'ad' => $urun->getAd(),
            'kategori_id' => $urun->getKategoriId(),
            'alis_fiyati' => $urun->getAlisFiyati(),
            'satis_fiyati' => $urun->getSatisFiyati(),
            'stok_miktari' => $stok
        ]), ENT_QUOTES, 'UTF-8');

        echo '<tr>
                <td><span class="badge bg-light text-dark border">' . htmlspecialchars($urun->getBarkod()) . '</span></td>
                <td class="fw-bold">' . htmlspecialchars($urun->getAd()) . '</td>
                <td><span class="badge bg-secondary">' . htmlspecialchars($urun->kategori_adi ?? 'Genel') . '</span></td>
                <td>₺' . number_format($urun->getAlisFiyati(), 2) . '</td>
                <td class="fw-bold text-success">₺' . number_format($urun->getSatisFiyati(), 2) . '</td>
                <td>' . $stokBadge . '</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="urunDuzenlePenceresi(' . $urunJson . ')" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="urunSil(' . $urun->getId() . ', \'' . addslashes($urun->getAd()) . '\')" title="Sil">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-warning text-center py-4"><i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>Arama kriterlerine uygun ürün bulunamadı.</div>';
}
?>
