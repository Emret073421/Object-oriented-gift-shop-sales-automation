<?php 
$page_title = 'Ürün Yönetimi';
// header is now included in index.php
?>

<div class="card card-custom bg-white mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Sistemdeki Ürünler</h6>
        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Yeni Ürün Ekle</button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Ürün Ara (Barkod veya Ad)...">
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option>Tüm Kategoriler</option>
                    <option>Kupalar</option>
                    <option>Magnetler</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
            <table class="table table-hover align-middle border">
                <thead class="table-light">
                    <tr>
                        <th>Barkod</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th>Alış F.</th>
                        <th>Satış F.</th>
                        <th>Stok</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>869000000001</td>
                        <td>İstanbul Temalı Kupa</td>
                        <td><span class="badge bg-secondary">Kupalar</span></td>
                        <td>₺25.00</td>
                        <td class="fw-bold">₺50.00</td>
                        <td><span class="badge bg-success">100 Adet</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>869000000006</td>
                        <td>Kız Kulesi Işıklı Kar Küresi</td>
                        <td><span class="badge bg-secondary">Kar Küreleri</span></td>
                        <td>₺85.00</td>
                        <td class="fw-bold">₺150.00</td>
                        <td><span class="badge bg-warning text-dark">45 Adet</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

// footer is now included in index.php
