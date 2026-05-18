<?php
require_once '../config.php';

$kategoriManager = new KategoriManager($db);
$kategoriler = $kategoriManager->getir();

if ($kategoriler && count($kategoriler) > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle bg-white shadow-sm rounded border mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Kategori Adı</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th class="pe-4 text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($kategoriler as $kat) {
        $katJson = htmlspecialchars(json_encode([
            'id' => $kat->getId(),
            'ad' => $kat->getAd(),
            'aciklama' => $kat->getAciklama()
        ]), ENT_QUOTES, 'UTF-8');

        echo '<tr>
                <td class="ps-4 fw-bold text-dark">' . htmlspecialchars($kat->getAd()) . '</td>
                <td class="text-muted">' . htmlspecialchars($kat->getAciklama() ?: 'Açıklama yok') . '</td>
                <td><span class="badge bg-success fs-7">Aktif</span></td>
                <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="kategoriDuzenlePenceresi(' . $katJson . ')" title="Düzenle">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="kategoriSil(' . $kat->getId() . ', \'' . addslashes($kat->getAd()) . '\')" title="Sil">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-warning text-center py-4 mb-0"><i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>Kayıtlı aktif kategori bulunamadı.</div>';
}
?>
