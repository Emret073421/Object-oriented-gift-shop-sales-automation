<?php 
$page_title = 'Kategori Yönetimi';
// header is now included in index.php
?>

<div class="row">
    <div class="col-md-4">
        <div class="card card-custom bg-white mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Yeni Kategori Ekle</h6>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" placeholder="Örn: Seramikler">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama (Opsiyonel)</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-2"></i>Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-custom bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Mevcut Kategoriler</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kategori Adı</th>
                                <th>Açıklama</th>
                                <th>Durum</th>
                                <th class="pe-4 text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 fw-bold">Kupalar</td>
                                <td class="text-muted">Baskılı ve özel tasarım kupalar</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Kar Küreleri</td>
                                <td class="text-muted">Müzikli ve ışıklı kar küreleri</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

// footer is now included in index.php
