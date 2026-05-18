<?php 
$page_title = 'Personel ve Kasiyer Yönetimi';
// header is now included in index.php
?>

<div class="row">
    <div class="col-md-8">
        <div class="card card-custom bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Kayıtlı Personeller</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Ad Soyad</th>
                                <th>Kullanıcı Adı</th>
                                <th>Yetki Seviyesi</th>
                                <th>Durum</th>
                                <th class="pe-4 text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 fw-bold">Ahmet Yılmaz</td>
                                <td>@admin</td>
                                <td><span class="badge bg-danger">YÖNETİCİ</span></td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Ayşe Demir</td>
                                <td>@kasiyer_ayse</td>
                                <td><span class="badge bg-info text-dark">KASİYER</span></td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 fw-bold">Mehmet Kaya</td>
                                <td>@kasiyer_mehmet</td>
                                <td><span class="badge bg-info text-dark">KASİYER</span></td>
                                <td><span class="badge bg-secondary">Pasif</span></td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-sm btn-outline-success"><i class="fa-solid fa-check"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card card-custom bg-white mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Yeni Personel Ekle</h6>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı (Giriş için)</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifre</label>
                        <input type="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yetki</label>
                        <select class="form-select">
                            <option value="KASIYER">Kasiyer</option>
                            <option value="PERSONEL">Standart Personel</option>
                            <option value="YONETICI">Yönetici</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-user-plus me-2"></i>Personeli Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>

// footer is now included in index.php
