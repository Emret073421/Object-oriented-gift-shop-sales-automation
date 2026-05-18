<?php
// IslemManager üzerinden istatistikleri ve kritik stokları çekiyoruz
$islemManager = new IslemManager($db);
$ozet = $islemManager->dashboardOzet();
$kritikUrunler = $islemManager->kritikStokGetir(50); // 50 adet ve altı
?>
<!-- ÜST BİLGİ KARTLARI -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-primary h-100 shadow-sm">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Günlük / Toplam Ciro</div>
                <h3 class="fw-bold text-dark mb-1">₺<?= number_format($ozet['gunluk_ciro'], 2) ?></h3>
                <small class="text-primary fw-semibold">Toplam: ₺<?= number_format($ozet['toplam_ciro'], 2) ?></small>
                <i class="fa-solid fa-wallet stat-icon text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-success h-100 shadow-sm">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Günlük / Toplam İşlem</div>
                <h3 class="fw-bold text-dark mb-1"><?= $ozet['gunluk_islem'] ?> Adet</h3>
                <small class="text-success fw-semibold">Toplam: <?= $ozet['toplam_islem'] ?> İşlem</small>
                <i class="fa-solid fa-receipt stat-icon text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-warning h-100 shadow-sm">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Değişim / İade Sayısı</div>
                <h3 class="fw-bold text-dark mb-0"><?= $ozet['iade_degisim'] ?> Adet</h3>
                <i class="fa-solid fa-right-left stat-icon text-warning"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-info h-100 shadow-sm">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Toplam Ürün Çeşidi</div>
                <h3 class="fw-bold text-dark mb-0"><?= $ozet['toplam_cesit'] ?> Çeşit</h3>
                <i class="fa-solid fa-box-open stat-icon text-info"></i>
            </div>
        </div>
    </div>
</div>

<!-- ALT KISIM: TÜKENMEKTE OLAN ÜRÜNLER TABLOSU -->
<div class="row">
    <div class="col-12">
        <div class="card card-custom bg-white border-top border-4 border-danger shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Kritik Stok Uyarıları (50 Adet ve Altı Ürünler)</h6>
                <a href="index.php?sayfa=urunler" class="btn btn-sm btn-outline-danger">Stokları Yönet</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Barkod</th>
                                <th>Ürün Adı</th>
                                <th>Kategori</th>
                                <th>Satış Fiyatı</th>
                                <th>Kalan Stok</th>
                                <th class="pe-4 text-end">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($kritikUrunler)): ?>
                                <?php foreach ($kritikUrunler as $urun): ?>
                                    <?php 
                                        $stok = (int)$urun['stok_miktari'];
                                        if ($stok <= 15) {
                                            $trClass = 'table-danger';
                                            $badgeClass = 'bg-danger';
                                            $durumText = '<span class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i>Tükeniyor</span>';
                                        } elseif ($stok <= 35) {
                                            $trClass = 'table-warning';
                                            $badgeClass = 'bg-warning text-dark';
                                            $durumText = '<span class="text-warning-emphasis fw-bold"><i class="fa-solid fa-bell me-1"></i>Azaldı</span>';
                                        } else {
                                            $trClass = 'table-secondary';
                                            $badgeClass = 'bg-info text-dark';
                                            $durumText = '<span class="text-info-emphasis fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Dikkat</span>';
                                        }
                                    ?>
                                    <tr class="<?= $trClass ?>">
                                        <td class="ps-4"><span class="text-muted"><?= htmlspecialchars($urun['barkod'] ?? 'Barkodsuz') ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($urun['ad']) ?></td>
                                        <td><?= htmlspecialchars($urun['kategori_adi'] ?? 'Kategorisiz') ?></td>
                                        <td>₺<?= number_format($urun['satis_fiyati'], 2) ?></td>
                                        <td><span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2 fs-6"><?= $stok ?> Adet</span></td>
                                        <td class="pe-4 text-end"><?= $durumText ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-check-circle text-success me-2"></i>Kritik stok seviyesinde ürün bulunmamaktadır.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>