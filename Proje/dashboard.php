<!-- ÜST BİLGİ KARTLARI -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-primary h-100">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Günlük Ciro</div>
                <h3 class="fw-bold text-dark mb-0">₺4,500.00</h3>
                <i class="fa-solid fa-wallet stat-icon text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-success h-100">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Bugünkü İşlem Sayısı</div>
                <h3 class="fw-bold text-dark mb-0">42 Adet</h3>
                <i class="fa-solid fa-receipt stat-icon text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-warning h-100">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Değişim / İade</div>
                <h3 class="fw-bold text-dark mb-0">3 Adet</h3>
                <i class="fa-solid fa-right-left stat-icon text-warning"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-white border-start border-4 border-info h-100">
            <div class="card-body">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Toplam Ürün Çeşidi</div>
                <h3 class="fw-bold text-dark mb-0">120 Çeşit</h3>
                <i class="fa-solid fa-box-open stat-icon text-info"></i>
            </div>
        </div>
    </div>
</div>

<!-- ALT KISIM: TÜKENMEKTE OLAN ÜRÜNLER TABLOSU -->
<div class="row">
    <div class="col-12">
        <div class="card card-custom bg-white border-top border-4 border-danger">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Kritik Stok Uyarıları (Tükenmekte Olan Ürünler)</h6>
                <a href="" class="btn btn-sm btn-outline-danger">Stokları Yönet</a>
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
                            <tr class="table-danger">
                                <td class="ps-4"><span class="text-muted">869000000008</span></td>
                                <td class="fw-bold">El Yapımı Çini Kase</td>
                                <td>Biblolar & Seramik</td>
                                <td>₺250.00</td>
                                <td><span class="badge bg-danger rounded-pill px-3 py-2 fs-6">3 Adet</span></td>
                                <td class="pe-4 text-end"><span class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i>Tükeniyor</span></td>
                            </tr>
                            <tr class="table-warning">
                                <td class="ps-4"><span class="text-muted">869000000006</span></td>
                                <td class="fw-bold">Kız Kulesi Işıklı Kar Küresi</td>
                                <td>Kar Küreleri</td>
                                <td>₺150.00</td>
                                <td><span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">8 Adet</span></td>
                                <td class="pe-4 text-end"><span class="text-warning-emphasis fw-bold"><i class="fa-solid fa-bell me-1"></i>Azaldı</span></td>
                            </tr>
                            <tr class="table-warning">
                                <td class="ps-4"><span class="text-muted">869000000007</span></td>
                                <td class="fw-bold">Semazen Biblosu (Büyük Boy)</td>
                                <td>Biblolar & Seramik</td>
                                <td>₺120.00</td>
                                <td><span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">12 Adet</span></td>
                                <td class="pe-4 text-end"><span class="text-warning-emphasis fw-bold"><i class="fa-solid fa-bell me-1"></i>Azaldı</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>