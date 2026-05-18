<?php 
$page_title = 'Personel ve Kasiyer Yönetimi';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row g-4 mb-4">
    <!-- SOL BÖLÜM: Mevcut Personeller Tablosu -->
    <div class="col-lg-8">
        <div class="card card-custom bg-white border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-users text-primary me-2"></i> Kayıtlı Aktif Personeller</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="personelleriGetir()" title="Yenile">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
            <div class="card-body p-4">
                <div id="personelListesiAlani" style="min-height: 350px;">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Personeller yükleniyor...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SAĞ BÖLÜM: Yeni Personel Ekleme Formu -->
    <div class="col-lg-4">
        <div class="card card-custom bg-white border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-user-plus text-success me-2"></i> Yeni Personel Ekle</h5>
            </div>
            <div class="card-body p-4">
                <form id="personelEkleForm" onsubmit="personelEkle(event)">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Ad <span class="text-danger">*</span></label>
                        <input type="text" id="ekleAd" class="form-control py-2 shadow-sm" placeholder="Örn: Ahmet" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Soyad <span class="text-danger">*</span></label>
                        <input type="text" id="ekleSoyad" class="form-control py-2 shadow-sm" placeholder="Örn: Yılmaz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Kullanıcı Adı (Giriş için) <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted">@</span>
                            <input type="text" id="ekleKadi" class="form-control border-start-0 ps-0" placeholder="kullanici_adi" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Şifre <span class="text-danger">*</span></label>
                        <input type="password" id="ekleSifre" class="form-control py-2 shadow-sm" placeholder="••••••••" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted mb-1">Yetki Seviyesi</label>
                        <select id="ekleYetki" class="form-select py-2 shadow-sm">
                            <option value="KASIYER">Kasiyer</option>
                            <option value="PERSONEL">Standart Personel</option>
                            <option value="YÖNETİCİ">Yönetici</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-user-plus me-2"></i> Personeli Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    personelleriGetir();
});

// 1. Personelleri AJAX ile Getirme
function personelleriGetir() {
    fetch("ajax/personel_getir.php")
    .then(response => response.text())
    .then(data => {
        document.getElementById('personelListesiAlani').innerHTML = data;
    })
    .catch(error => console.error("Personel getirme hatası:", error));
}

// 2. Yeni Personel Ekleme
function personelEkle(event) {
    event.preventDefault();

    let ad = document.getElementById('ekleAd').value;
    let soyad = document.getElementById('ekleSoyad').value;
    let kadi = document.getElementById('ekleKadi').value;
    let sifre = document.getElementById('ekleSifre').value;
    let yetki = document.getElementById('ekleYetki').value;

    let formData = new FormData();
    formData.append('ad', ad);
    formData.append('soyad', soyad);
    formData.append('kadi', kadi);
    formData.append('sifre', sifre);
    formData.append('yetki', yetki);

    fetch("ajax/personel_ekle.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.basarili) {
            Swal.fire('Başarılı!', data.mesaj, 'success').then(() => {
                document.getElementById('personelEkleForm').reset();
                personelleriGetir();
            });
        } else {
            Swal.fire('Hata!', data.mesaj, 'error');
        }
    })
    .catch(error => {
        console.error("Ekleme hatası:", error);
        Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
    });
}

// 3. Personel Düzenleme Penceresi
function personelDuzenlePenceresi(p) {
    Swal.fire({
        title: 'Personel Düzenle',
        html: `
            <div class="text-start mb-3">
                <label class="fw-bold form-label small">Ad:</label>
                <input type="text" id="duzenleAd" class="form-control py-2" value="${p.ad}" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label small">Soyad:</label>
                <input type="text" id="duzenleSoyad" class="form-control py-2" value="${p.soyad}" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label small">Kullanıcı Adı:</label>
                <input type="text" id="duzenleKadi" class="form-control py-2" value="${p.kullanici_adi}" required>
            </div>
            <div class="text-start mb-3">
                <label class="fw-bold form-label small">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın):</label>
                <input type="password" id="duzenleSifre" class="form-control py-2" placeholder="••••••••">
            </div>
            <div class="text-start mb-2">
                <label class="fw-bold form-label small">Yetki Seviyesi:</label>
                <select id="duzenleYetki" class="form-select py-2">
                    <option value="KASIYER" ${p.yetki === 'KASIYER' ? 'selected' : ''}>Kasiyer</option>
                    <option value="PERSONEL" ${p.yetki === 'PERSONEL' ? 'selected' : ''}>Standart Personel</option>
                    <option value="YÖNETİCİ" ${p.yetki === 'YÖNETİCİ' || p.yetki === 'YONETICI' ? 'selected' : ''}>Yönetici</option>
                </select>
            </div>
        `,
        icon: 'edit',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Güncelle',
        cancelButtonText: 'İptal',
        preConfirm: () => {
            let ad = document.getElementById('duzenleAd').value;
            let soyad = document.getElementById('duzenleSoyad').value;
            let kadi = document.getElementById('duzenleKadi').value;
            let sifre = document.getElementById('duzenleSifre').value;
            let yetki = document.getElementById('duzenleYetki').value;

            if (!ad || !soyad || !kadi) {
                Swal.showValidationMessage('Lütfen ad, soyad ve kullanıcı adı alanlarını doldurun.');
            }
            return { id: p.id, ad, soyad, kadi, sifre, yetki };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', result.value.id);
            formData.append('ad', result.value.ad);
            formData.append('soyad', result.value.soyad);
            formData.append('kadi', result.value.kadi);
            formData.append('sifre', result.value.sifre);
            formData.append('yetki', result.value.yetki);

            fetch("ajax/personel_guncelle.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Başarılı!', data.mesaj, 'success').then(() => personelleriGetir());
                } else {
                    Swal.fire('Hata!', data.mesaj, 'error');
                }
            })
            .catch(error => {
                console.error("Güncelleme hatası:", error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}

// 4. Personel Silme (Arşivleme)
function personelSil(id, adSoyad) {
    Swal.fire({
        title: 'Personeli Pasife Al',
        html: `<b>${adSoyad}</b> isimli personelin sisteme girişini engellemek (arşivlemek) istediğinize emin misiniz?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Pasife Al',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', id);

            fetch("ajax/personel_sil.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.basarili) {
                    Swal.fire('Pasife Alındı!', data.mesaj, 'success').then(() => personelleriGetir());
                } else {
                    Swal.fire('Hata!', data.mesaj, 'error');
                }
            })
            .catch(error => {
                console.error("Silme hatası:", error);
                Swal.fire('Hata!', 'Sunucu ile iletişim kurulurken bir hata oluştu.', 'error');
            });
        }
    });
}
</script>
