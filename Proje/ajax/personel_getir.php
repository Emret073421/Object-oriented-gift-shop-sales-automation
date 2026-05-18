<?php
require_once '../config.php';

$personelManager = new PersonelManager($db);
$personeller = $personelManager->getir();

if ($personeller && count($personeller) > 0) {
    echo '<div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Ad Soyad</th>
                    <th>Kullanıcı Adı</th>
                    <th>Yetki Seviyesi</th>
                    <th>Durum</th>
                    <th class="pe-4 text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($personeller as $p) {
        $adSoyad = htmlspecialchars($p['ad'] . ' ' . $p['soyad']);
        $kadi = htmlspecialchars($p['kullanici_adi']);
        
        $yetkiBadge = '';
        if ($p['yetki'] === 'YONETICI' || $p['yetki'] === 'YÖNETİCİ') {
            $yetkiBadge = '<span class="badge bg-danger">YÖNETİCİ</span>';
        } elseif ($p['yetki'] === 'KASIYER' || $p['yetki'] === 'KASİYER') {
            $yetkiBadge = '<span class="badge bg-info text-dark">KASİYER</span>';
        } else {
            $yetkiBadge = '<span class="badge bg-secondary">' . htmlspecialchars($p['yetki']) . '</span>';
        }

        $pJson = htmlspecialchars(json_encode([
            'id' => (int)$p['id'],
            'ad' => $p['ad'],
            'soyad' => $p['soyad'],
            'kullanici_adi' => $p['kullanici_adi'],
            'yetki' => $p['yetki']
        ]), ENT_QUOTES, 'UTF-8');

        echo '<tr>
                <td class="ps-4 fw-bold text-dark">' . $adSoyad . '</td>
                <td><span class="badge bg-light text-dark border">@' . $kadi . '</span></td>
                <td>' . $yetkiBadge . '</td>
                <td><span class="badge bg-success">Aktif</span></td>
                <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="personelDuzenlePenceresi(' . $pJson . ')" title="Düzenle">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="personelSil(' . (int)$p['id'] . ', \'' . addslashes($adSoyad) . '\')" title="Sil">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
              </tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-warning text-center py-4 mb-0"><i class="fa-solid fa-user-xmark fa-2x mb-2 d-block"></i>Kayıtlı aktif personel bulunamadı.</div>';
}
?>
